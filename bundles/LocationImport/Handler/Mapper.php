<?php


namespace LocationImportBundle\Handler;


interface Mapper
{
    /**
     * Создать сущность из входного массива
     *
     * @param array $entity
     * @return object
     */
    public function makeEntity(array $entity): object;
}