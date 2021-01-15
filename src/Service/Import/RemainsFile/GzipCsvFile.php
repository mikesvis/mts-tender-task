<?php

namespace App\Service\Import\RemainsFile;

use http\Exception\RuntimeException;

/**
 * Class GzipCsvFile
 * @package App\Service\Import\RemainsFile
 */
class GzipCsvFile extends CsvFile
{
    /**
     * @var int Размер буфера
     */
    private const READ_BUFFER_SIZE = 4096;

    /**
     * GzipCsvFile constructor.
     * @param string $originalPath
     */
    public function __construct(public string $originalPath)
    {
        $destination = $this->getDestinationPath($originalPath);
        if (!file_exists($destination)) {
            $this->unpack($originalPath, $destination);
        }

        parent::__construct($destination);
    }

    /**
     * Распаковывает gz
     *
     * @param string $source
     * @param string $destination
     * @return void
     */
    private function unpack(string $source, string $destination): void
    {
        $sourceResource = $this->getGzResource($source);
        $destinationResource = $this->getFileResource($destination);

        while (!gzeof($sourceResource)) {
            fwrite($destinationResource, gzread($sourceResource, self::READ_BUFFER_SIZE));
        }
    }

    /**
     * Получает путь назначения
     *
     * @param string $source
     * @return string
     */
    private function getDestinationPath(string $source): string
    {
        return str_replace('.gz', '', $this->getTemporaryPath($source));
    }

    /**
     * Получает временный путь
     *
     * @param string $originalPath
     * @return string
     */
    private function getTemporaryPath(string $originalPath): string
    {
        return sprintf('%s/%s', sys_get_temp_dir(), basename($originalPath));
    }

    /**
     * Удаляет файлы
     */
    public function remove(): void
    {
        if (file_exists($this->originalPath)) {
            unlink($this->originalPath);
        }

        parent::remove();
    }

    /**
     * Получает resource для чтения из gz файла
     *
     * @param string $path
     * @return mixed
     */
    private function getGzResource(string $path): mixed
    {
        $resource = gzopen($path, 'rb');

        if (!$resource) {
            throw new RuntimeException(sprintf(
                'Не могу открыть файл для чтения %s',
                $path
            ));
        }

        return $resource;
    }

    /**
     * Получает resource на запись в файл
     *
     * @param string $path
     * @return mixed
     */
    private function getFileResource(string $path): mixed
    {
        $resource = fopen($path, 'wb');

        if (!$resource) {
            throw new RuntimeException(sprintf(
                'Не могу открыть файл для записи %s',
                $path
            ));
        }

        return $resource;
    }
}