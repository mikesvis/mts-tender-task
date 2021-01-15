<?php


namespace LocationImportBundle\Handler\Csv;


use LocationImportBundle\Handler\HandlerFactory;
use LocationImportBundle\Handler\Mapper;
use LocationImportBundle\Handler\Parser;
use LocationImportBundle\Util\RegionMapManager;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Фабрика обработчиков данных для csv
 */
class CsvHandlerFactoryRegion extends AbstractCsvHandlerFactory
{
    /**
     * @inheritDoc
     */
    public function getMapper(): Mapper
    {
        return new CsvMapperRegion();
    }
}