<?php

namespace App\Console\Commands;

use App\Entity\Warehouse;
use App\Service\LogService;
use App\Service\Statistics\StatisticsService;
use App\Service\Api\WarehouseService;
use App\Service\Import\ImportService;
use App\Service\Import\RemainsFile\CsvFile;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Doctrine\DBAL\Driver\Connection;

/**
 * Команда обработки файла импорта
 *
 * @package App\Console\Commands
 */
class ImportProcessFileCommand extends Command
{
    /**
     * @var string Название команды
     */
    public const SERVICE_NAME = 'import:process-file';

    /**
     * @var CsvFile
     */
    private CsvFile $csvFile;

    /**
     * @var int Опция "import-id"
     */
    private int $importId;

    /**
     * @var Warehouse
     */
    private Warehouse $warehouse;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * ImportProcessFileCommand constructor
     *
     * @param ImportService $importService
     * @param WarehouseService $warehouseService
     * @param StatisticsService $statisticsService
     * @param Connection $dbConnection
     */
    public function __construct(
        private ImportService $importService,
        private WarehouseService $warehouseService,
        private StatisticsService $statisticsService,
        private Connection $dbConnection
    )
    {
        parent::__construct();
    }

    /** @inheritDoc */
    protected function configure()
    {
        $this
            ->setName(static::SERVICE_NAME)
            ->setDescription('Процесс обработки файла')
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_REQUIRED,
                'Путь к файлу для обработки'
            )
            ->addOption(
                'import-id',
                'i',
                InputOption::VALUE_REQUIRED,
                'Идентификатор процесса'
            );
    }

    /** @inheritDoc */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->csvFile = new CsvFile($input->getOption('path'));
        if (!file_exists($this->csvFile->getPath())) {
            throw new FileNotFoundException("Файл " . $this->csvFile->getPath() . " не найден");
        }

        $this->importId = (int)$input->getOption('import-id');
        $this->logger = new LogService(self::SERVICE_NAME);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->setWarehouse();
            $this->fileHandler();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

    /**
     * Получение склада
     *
     * @throws \Exception
     */
    protected function setWarehouse(): void
    {
        try {
            $warehouseId = $this->csvFile->getWarehouse();
            $this->warehouse = $this->warehouseService->getById($warehouseId)[0];
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Обработка файла %s: %s', $this->csvFile->getPath(), $e->getMessage()));
        }
    }

    /**
     * Обработка файла
     *
     * @throws \Exception
     */
    protected function fileHandler(): void
    {
        $handle = fopen($this->csvFile->getPath(), 'r');
        $lock = flock($handle, LOCK_EX | LOCK_NB);

        if ($lock) {
            $dataCollection = new ArrayCollection();
            while (($row = fgetcsv($handle, 1000, ";")) !== false) {
                $dataCollection->add($row);
            }
            flock($handle, LOCK_UN);
            fclose($handle);

            try {
                $this->importService->save($dataCollection, $this->warehouse, $this->csvFile->getTime());
            } catch (\Exception $exception) {
                $errorsCount = $dataCollection->count();
            }

            $this->setStat($dataCollection->count(), isset($errorsCount) ? $errorsCount : 0);
        } else {
            throw new \Exception(sprintf('Обработка файла %s: файл не доступен', $this->csvFile->getPath()));
        }
    }

    /**
     * Обновляем статистику
     *
     * @param int $countTotal
     * @param int $countError
     * @throws \Exception
     */
    protected function setStat(int $countTotal, int $countError)
    {
        $this->statisticsService->addCount($this->importId, $countTotal, $countError);
    }
}