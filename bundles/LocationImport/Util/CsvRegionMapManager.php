<?php


namespace LocationImportBundle\Util;


use App\Entity\Region;
use LocationImportBundle\Service\ServiceRegion;

/**
 * Класс получения массива региона для заполнения таблицы городов
 */
class CsvRegionMapManager implements RegionMapManager
{
    /** @var Region[] Массив существующих в базе регионов */
    protected static array $regionMap = [];

    /**
     * CsvRegionMapManager конструктор
     *
     * @param ServiceRegion $serviceRegion Сервис для работы с данными регионов
     */
    public function __construct(protected ServiceRegion $serviceRegion)
    {
    }

    /**
     * Заполнить массив регионов
     */
    public function getRegionMap(): array
    {
        if (empty(self::$regionMap)) {
            /** @var Region[] $regions */
            $regions = $this->serviceRegion->findAll();
            self::$regionMap = [];
            foreach ($regions as $region) {
                self::$regionMap[$region->getName()] = $region;
            }
        }

        return self::$regionMap;
    }
}