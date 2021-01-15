<?php

namespace App\Service\Import\RemainsFile;

/**
 * Class CsvFile
 * @package App\Service\Import\RemainsFile
 */
class CsvFile implements RemainsFileInterface
{
    public function __construct(private string $path)
    {

    }

    /**
     * Возвращает путь файла
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Получает время из файла
     * @return \DateTime
     */
    public function getTime(): \DateTime
    {
        preg_match_all('/remains_(?:full|partial)_(?:[A-Za-z0-9]+)_(\d{4}_\d{2}_\d{2}_\d{2}_\d{2}_\d{2})/', $this->getPath(), $matches);
        return \DateTime::createFromFormat('Y_m_d_H_i_s', $matches[1][0]);
    }

    /**
     * Получает идентификатор склада
     * @return string
     */
    public function getWarehouse(): string
    {
        preg_match_all('/remains_(?:full|partial)_([A-Za-z0-9]+)_\d{4}_\d{2}_\d{2}_\d{2}_\d{2}_\d{2}/', $this->getPath(), $matches);
        return $matches[1][0];
    }

    /**
     * Удаляет файл
     */
    public function remove(): void
    {
        if (file_exists($this->getPath())) {
            unlink($this->getPath());
        }
    }
}