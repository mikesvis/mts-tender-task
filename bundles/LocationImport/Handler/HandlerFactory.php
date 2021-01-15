<?php


namespace LocationImportBundle\Handler;

/**
 * Фабрика обработчиков входных данных
 */
interface HandlerFactory
{
    /**
     * Получить реализацию парсера
     *
     * @return Parser
     */
    public function getParser(): Parser;

    /**
     * Получить реализацию маппера
     *
     * @return Mapper
     */
    public function getMapper(): Mapper;
}