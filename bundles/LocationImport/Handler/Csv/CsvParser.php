<?php


namespace LocationImportBundle\Handler\Csv;


use LocationImportBundle\Handler\Parser;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Парсер для входных данных из csv
 */
class CsvParser implements Parser
{
    /** @var string Директория проекта */
    private string $directory = "";

    /**
     * CsvParser конструктор
     *
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * @inheritDoc
     */
    public function parse(mixed $filePath): array
    {
        if (empty((string) $filePath)) {
            throw new \InvalidArgumentException("Параметр filePath должен быть не пустой строкой");
        }
        $filePath = $this->directory . $filePath;
        if (!file_exists($filePath)) {
            throw new FileNotFoundException("Файл $filePath не найден");
        }

        $data = [];
        $keys = [];
        $i = 0;
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($i === 0) {
                    $keys = $row;
                } else {
                    $data[] = array_combine($keys, $row);
                }
                $i++;
            }
            fclose($handle);
        }
        return $data;
    }
}