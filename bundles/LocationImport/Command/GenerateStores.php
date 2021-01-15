<?php


namespace LocationImportBundle\Command;


use LocationImportBundle\Service\ServiceWarehouse;
use LocationImportBundle\Util\RegionMapManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Сгенерировать склады с привязками к городу
 */
class GenerateStores extends Command
{
    /**
     * GenerateStores конструктор
     * @param string|null $name Имя команды
     * @param ServiceWarehouse $warehouseService Сервис для работы с данными складов
     */
    public function __construct(
        string $name = null,
        private ServiceWarehouse $warehouseService
    )
    {
        $this->warehouseService = $warehouseService;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setName('fill:stores')
            ->setDescription('Заполнение таблицы warehouse тестовыми данными');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Запускаем очистку данных...");
        $output->writeln("Очистка таблицы warehouse...");
        $this->warehouseService->truncate();
        $output->writeln("Заполнение таблицы warehouse...");
        try {
            $this->warehouseService->fillData();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

}