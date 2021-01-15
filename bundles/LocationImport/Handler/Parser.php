<?php


namespace LocationImportBundle\Handler;

/**
 * Интерфейс для парсера входных данных
 */
interface Parser
{
    /**
     * Распарсить данные
     *
     * @param mixed $resource Источник
     * @return array
     */
    public function parse(mixed $resource): array;
}