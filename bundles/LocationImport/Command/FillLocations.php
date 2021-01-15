<?php

namespace LocationImportBundle\Command;

use LocationImportBundle\Service\ServiceCity;
use LocationImportBundle\Service\ServiceRegion;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда заполнения данных по городам и регионам
 */
class FillLocations extends Command
{
    /**
     * FillLocations конструктор
     *
     * @param string|null $name Название команды
     * @param ServiceRegion $serviceRegion Сервис для работы с данными региона
     * @param ServiceCity $serviceCity Сервис для работы с данными города
     */
    public function __construct(
        string $name = null,
        private ServiceRegion $serviceRegion,
        private ServiceCity $serviceCity
    )
    {
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setName('fill:locations')
            ->setDescription('Заполнение таблиц city и region');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Запускаем очистку данных...");
        $output->writeln("Очистка таблицы region...");
        $this->serviceRegion->clear();
        $output->writeln("Очистка таблицы city...");
        $this->serviceCity->clear();
        $output->writeln("Заполнение таблицы region...");
        $this->serviceRegion->fill();
        $output->writeln("Заполнение таблицы city...");
        $this->serviceCity->fill();
        return self::SUCCESS;
    }
}