<?php


namespace LocationImportBundle\Handler\Csv;

use LocationImportBundle\Handler\Mapper;

/**
 * Общая логика мапперов
 */
abstract class AbstractCsvMapper implements Mapper
{
    /**
     * Очистить название региона от лишних символов
     *
     * @param $name
     * @return string
     */
    protected function sanitizeRegionName($name): string
    {
        $name = htmlentities($name);
        return preg_replace("~( -(.*))|( \/(.*))~", "", $name) ?? '';
    }
}