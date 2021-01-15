<?php


namespace LocationImportBundle\Service;


use LocationImportBundle\Handler\Mapper;
use LocationImportBundle\Handler\Parser;

/**
 * Общая логика сервисов для работы с данными регионов и городов
 */
abstract class AbstractServiceLocation
{
    /** @var Parser */
    protected Parser $parser;

    /** @var array */
    protected $errors = [];

    /**
     * Удалить все записи
     */
    abstract public function clear(): void;

    /**
     * Заполнить таблицу
     */
    abstract public function fill(): void;

    /**
     * Получить массив сущностей
     *
     * @return array
     */
    protected function getEntities(string $resource, Mapper $mapper): array
    {
        $entitiesRaw = $this->parser->parse($resource);
        $entities = [];
        foreach ($entitiesRaw as $entityRaw) {
            try {
                $entity = $mapper->makeEntity($entityRaw);
                $entities[] = $entity;
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }
        return $entities;
    }
}