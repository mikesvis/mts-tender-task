<?php


namespace LocationImportBundle\Util;

/**
 * Интерфейс для классов получения массива региона для заполнения таблицы городов
 */
interface RegionMapManager
{

    /**
     * Заполнить массив регионов
     */
    public function getRegionMap(): array;
}