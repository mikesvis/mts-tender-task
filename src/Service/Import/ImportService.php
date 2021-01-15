<?php

namespace App\Service\Import;

use App\Entity\Remains;
use App\Entity\Warehouse;
use App\Service\Import\RemainsFile\CsvFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;

class ImportService
{
    public const TYPE_FULL = 'full';

    /**
     * @var int Размер партии сохраняемых записей
     */
    public const BULK_SIZE = 1000;

    private \Doctrine\Persistence\ObjectRepository $remainsRepository;

    public function __construct(private EntityManagerInterface $entityManager, private Connection $dbConnection)
    {
        $this->remainsRepository = $entityManager->getRepository(Remains::class);
    }

    /**
     * Сохраняет данные из файла
     *
     * @param ArrayCollection $data
     * @param Warehouse $warehouse
     * @param \DateTime $importTime
     */
    public function save(ArrayCollection $data, Warehouse $warehouse, \DateTime $importTime)
    {
        $updateTimeString = (new \DateTime)->format('Y-m-d H:i:s');
        $importTimeString = $importTime->format('Y-m-d H:i:s');

        $values = [];
        foreach ($data as $row) {
            $values["{$warehouse->getId()}|{$row[0]}"] = "('{$row[0]}', '{$warehouse->getId()}', {$row[1]}, '{$updateTimeString}', '{$importTimeString}')";
        }

        $this->dbConnection->exec('
            INSERT INTO `remains`
            (`product_id`, `warehouse_id`, `count`, `date_update`, `date_import`)
            VALUES
            ' . implode(',', $values) . '
            ON DUPLICATE KEY UPDATE
                `count` = VALUES(`count`),
                `date_update` = VALUES(`date_update`),
                `date_import` = VALUES(`date_import`);
        ');
    }

    /**
     * Удаляет старые записи
     *
     * @param ImportConfiguration $configuration
     * @return int
     */
    public function cleanOld(ImportConfiguration $configuration): int
    {
        $startImportDateTime = (new \DateTime())->setTimestamp($configuration->start);
        $totalDeletedRows = 0;
        while (true) {
            $deletedRowsCount = $this->remainsRepository->deleteOld(
                $startImportDateTime,
                $configuration->dbCleanStepCount
            );
            if ($deletedRowsCount === 0) {
                break;
            }
            $totalDeletedRows += $deletedRowsCount;
        }

        return $totalDeletedRows;
    }

    /**
     * Удаляет файлы коллекции
     * @param Collection $files
     */
    public function removeFiles(Collection $files): void
    {
        /** @var CsvFile $file */
        foreach ($files as $file) {
            $file->remove();
        }
    }
}