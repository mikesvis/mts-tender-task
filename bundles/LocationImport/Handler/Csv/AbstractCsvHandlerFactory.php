<?php


namespace LocationImportBundle\Handler\Csv;


use LocationImportBundle\Handler\HandlerFactory;
use LocationImportBundle\Handler\Mapper;
use LocationImportBundle\Handler\Parser;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Общая логика для обработчиков данных из csv
 */
abstract class AbstractCsvHandlerFactory implements HandlerFactory
{
    /**
     * CsvParser конструктор
     *
     * @param KernelInterface $appKernel
     */
    public function __construct(
        private KernelInterface $appKernel,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function getParser(): Parser
    {
        return new CsvParser($this->appKernel->getProjectDir());
    }

    /**
     * @inheritDoc
     */
    abstract public function getMapper(): Mapper;
}