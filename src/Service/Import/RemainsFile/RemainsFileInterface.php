<?php
namespace App\Service\Import\RemainsFile;

/**
 * Interface RemainsFileInterface
 * 
 * @package App\Service\Import
 */
interface RemainsFileInterface
{
    public function getPath();

    public function getWarehouse();

    public function getTime();

    public function remove();
}