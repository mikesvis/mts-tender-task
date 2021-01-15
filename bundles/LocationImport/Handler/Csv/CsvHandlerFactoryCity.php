<?php


namespace LocationImportBundle\Handler\Csv;


use LocationImportBundle\Handler\Mapper;
use LocationImportBundle\Util\RegionMapManager;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Фабрика обработчиков данных для csv
 */
class CsvHandlerFactoryCity extends AbstractCsvHandlerFactory
{
    /**
     * CsvParser конструктор
     *
     * @param KernelInterface $appKernel
     */
    public function __construct(
        private KernelInterface $appKernel,
        private RegionMapManager $regionMapManager
    )
    {
        parent::__construct($this->appKernel);
    }

    /**
     * @inheritDoc
     */
    public function getMapper(): Mapper
    {
        return new CsvMapperCity($this->regionMapManager);
    }
}