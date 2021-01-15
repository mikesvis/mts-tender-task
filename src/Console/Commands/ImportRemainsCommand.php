<?php

namespace App\Console\Commands;

use App\Console\Traits\MultiProcessTrait;
use App\Service\Import\ImportConfiguration;
use App\Service\Import\ImportService;
use App\Service\Import\RemainsFile\GzipCsvFile;
use App\Service\LogService;
use App\Service\Statistics\Result;
use App\Service\Statistics\StatisticsService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;
use Psr\Log\LoggerInterface;
use \RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Throwable;

class ImportRemainsCommand extends Command
{
    use MultiProcessTrait;

    private const SERVICE_NAME = 'import:run';

    private ImportConfiguration $config;
    private LoggerInterface $logger;
    private OutputInterface $output;

    public function __construct(private ImportService $importService, private StatisticsService $statisticsService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(static::SERVICE_NAME)
            ->setDescription('Импорт остатков атрикулов по складам из файлов в указанной директории.')
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Директория с архивом файлов импорта',
                '/var/www/test-task/import'
            )
            ->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'Тип импорта (full, partial)',
                ImportService::TYPE_FULL
            )
            ->addOption(
                'max-threads',
                'mt',
                InputOption::VALUE_OPTIONAL,
                'Максимальное колчество процессов для обработки файлов',
                20
            )
            ->addOption(
                'threads-pause',
                'tp',
                InputOption::VALUE_OPTIONAL,
                'Пауза (мс) если все процессы заняты',
                5
            )
            ->addOption(
                'db-clean-step-count',
                'cs',
                InputOption::VALUE_OPTIONAL,
                'Количество удаляемых за итерацию строк из БД после полного импорта',
                1000
            )
            ->addOption(
                'db-clean-pause',
                'cp',
                InputOption::VALUE_OPTIONAL,
                'Пауза между удалениями из БД (мс)',
                1
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->output = $output;
        $this->config = new ImportConfiguration($input);
        $this->logger = new LogService(self::SERVICE_NAME);
        $this->setSignals();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->isProcessAlreadyRunning()) {
            $this->output->writeln('<error>Импорт уже запущен в другом процессе');
            return Command::FAILURE;
        }
        try {
            $files = $this->getFiles();
            if ($files->isEmpty()) {
                $this->output->writeln(sprintf(
                    '<info>Нет файлов импорта типа %s в директории %s',
                    $this->config->type,
                    $this->config->path
                ));
                return Command::SUCCESS;
            }

            $result = $this->createStat();
            $this->runProcesses($files);
            $this->finishProcesses();
            $this->cleanOldRemains($result);
            $this->removeFiles($files);
            $this->finishStat($result);
            $this->showResult();
        } catch (RuntimeException $ex) {
            $this->logger->error($ex->getMessage());
        } catch (Throwable $t) {
            $this->output->writeln('<error>' . $t->getMessage() . $t->getFile() . $t->getLine());
            $this->logger->error(
                $t->getMessage(),
                [
                    $t->getFile(),
                    $t->getLine(),
                    $t->getTrace(),
                ]
            );
        }

        return Command::SUCCESS;
    }

    /**
     * Существует ли другой процесс импорта в системе?
     *
     * @return bool
     */
    private function isProcessAlreadyRunning(): bool
    {
        return $this->countProcessInstancesByName(self::SERVICE_NAME) > 1;
    }

    /**
     * Сбор файлов импорта в коллекцию
     *
     * @return Collection
     */
    private function getFiles(): Collection
    {
        $collection = new ArrayCollection();

        foreach ($this->getFilePaths() as $path) {
            $collection->add(new GzipCsvFile($path));
        }

        return $collection;
    }

    /**
     * Получение списка файлов нужного типа из директории
     *
     * @return array
     */
    #[Pure] private function getFilePaths(): array
    {
        return glob(sprintf('%s/remains_%s_*.csv.gz', $this->config->path, $this->config->type));
    }

    private function createStat(): Result
    {
        $initialResult = new Result($this->config);
        $this->statisticsService->create($initialResult->timeStart, $initialResult->is_full);

        return $initialResult;
    }

    /**
     * Запуск процессов (команд) обработки файлов импорта
     *
     * @param Collection $files
     */
    private function runProcesses(Collection $files): void
    {
        $current = 0;
        while (true) {
            pcntl_signal_dispatch();

            if ($this->stop || !isset($files[$current])) {
                break;
            }

            if ($this->allThreadsAreBusy()) {
                usleep($this->config->threadsPause);
                continue;
            }

            $this->startFileProcess($files[$current]);
            $current++;
        }
    }

    /**
     * Все допустимые процессы заняты
     *
     * @return bool
     */
    private function allThreadsAreBusy(): bool
    {
        return $this->countChannels() >= $this->config->max_threads;
    }

    /**
     * Запуск одного процесса (команды) для обработки файла с остатками
     *
     * @param GzipCsvFile $file
     */
    private function startFileProcess(GzipCsvFile $file): void
    {
        $process = new Process($this->getCommand($file));
        $process->start();

        $this->addChannelByKey($this->getProcessKey($file), $process);
    }

    /**
     * Получение пути к консольной команде для процесса обработки файла остатков
     *
     * @param GzipCsvFile $file
     * @return array
     */
    private function getCommand(GzipCsvFile $file): array
    {
        return [
            'php',
            sprintf('%s/bin/console', getcwd()),
            $this->getProcessFileCommandName(),
            sprintf('--path=%s', $file->getPath()),
            sprintf('--import-id=%s', $this->config->start),
        ];
    }

    /**
     * Получение уникального ключа для процесса обработки файла с остатками на основании пути
     *
     * @param GzipCsvFile $file
     * @return string
     */
    #[Pure] private function getProcessKey(GzipCsvFile $file): string
    {
        return sprintf('%s::%s', self::SERVICE_NAME, substr(md5($file->getPath()), 0, 10));
    }

    /**
     * Очистка устаревших данных из базы при типе выгрузки full (полная выгрузка)
     *
     * @param Result $result
     */
    private function cleanOldRemains(Result $result): void
    {
        if ($this->config->type === ImportService::TYPE_FULL) {
            $result->deleted = $this->importService->cleanOld($this->config);
        }
    }

    /**
     * Удаление всех файлов, используемых при импорте
     *
     * @param $files
     */
    private function removeFiles($files): void
    {
        $this->importService->removeFiles($files);
    }

    /**
     * Записываем полседние данные в статистику
     *
     * @param Result $result
     * @throws \Exception
     */
    private function finishStat(Result $result): void
    {
        $this->statisticsService->finish(
            $result->timeStart,
            time(),
            $result->getLoadAverage(),
            $result->deleted
        );
    }

    /**
     * Выводим результат выгрузки
     *
     * @throws \Exception
     */
    private function showResult(): void
    {
        $statistics = $this->statisticsService->getById($this->config->start);
        $this->output->writeln(sprintf(
            '<info>Импорт закончен. Тип: %s, Время(сек): %s, Обновлено: %s, Удалено %s, Ошибок: %s, Нагрузка: %s',
            $this->config->type,
            $statistics->getTimeEnd() - $statistics->getTimeStart(),
            $statistics->getCountTotal(),
            $statistics->getRowsDeleted(),
            $statistics->getCountError(),
            number_format($statistics->getLoadAverage(), 2)
        ));
    }

    /**
     * Имя команды для обработки файла
     *
     * @return string
     */
    private function getProcessFileCommandName(): string
    {
        return ImportProcessFileCommand::SERVICE_NAME;
    }
}
