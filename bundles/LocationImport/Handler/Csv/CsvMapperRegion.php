<?php


namespace LocationImportBundle\Handler\Csv;


use App\Entity\Region;

/**
 * Класс для преобразования данных в сущность Регион
 */
class CsvMapperRegion extends AbstractCsvMapper
{
    /**
     * @inheritDoc
     */
    public function makeEntity(array $region): object
    {
        $regionEntity = new Region();

        $regionName = $this->sanitizeRegionName($region['name']);
        if (!$regionName) {
            throw new \InvalidArgumentException("Имя региона не должно быть пустым");
        }

        $regionEntity->setName($this->sanitizeRegionName($region['name']));

        return $regionEntity;
    }
}