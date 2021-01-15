<?php


namespace LocationImportBundle\Handler\Csv;


use App\Entity\City;
use App\Entity\Region;
use LocationImportBundle\Util\RegionMapManager;

/**
 * Класс для преобразования данных из csv в сущности
 */
class CsvMapperCity extends AbstractCsvMapper
{
    /**
     * CsvMapperCity конструктор
     *
     * @param RegionMapManager $regionMapManager
     */
    public function __construct(protected RegionMapManager $regionMapManager)
    {
    }

    /**
     * @inheritDoc
     */
    public function makeEntity(array $city): object
    {
        $cityEntity = new City();
        $cityEntity->setName($this->getCityName($city));
        $cityEntity->setRegion($this->getRegion($city));

        return $cityEntity;
    }

    /**
     * Получить название города
     *
     * @param array $city Массив данных города полученный из csv
     * @return string
     */
    protected function getCityName(array $city): string
    {
        if (!empty($city['city'])) {
            return htmlentities($city['city']);
        }

        $cityMarker = "г";
        $keyTypePostfix = "_type";
        $keysForChecking = [
            "settlement",
            "region",
        ];

        foreach ($keysForChecking as $key) {
            $keyType = $key . $keyTypePostfix;
            if (isset($city[$keyType]) && $city[$keyType] === $cityMarker) {
                $cityName = $city[$key];
                break;
            }
        }

        return htmlentities($cityName);
    }

    /**
     * Получить регион для города
     *
     * @param array $city Массив данных города полученный из csv
     * @return Region
     */
    protected function getRegion(array $city): Region
    {
        $regionMap = $this->regionMapManager->getRegionMap();
        $regionName = $this->sanitizeRegionName($city['region']);
        if (!isset($regionMap[$regionName])) {
            throw new \InvalidArgumentException('Не указан регион');
        }

        return $regionMap[$regionName];
    }
}