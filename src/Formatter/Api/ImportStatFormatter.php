<?php


namespace App\Formatter\Api;


use App\Entity\ImportStat;

/**
 * Класс для форматирования сущности ImportStat в массив для передачи в JsonResponce
 */
class ImportStatFormatter
{
    /**
     * Форматирование сущности в массив
     * @param ImportStat $importStat
     * @return array
     */
    public function formatImportStatToResponse(ImportStat $importStat): array
    {
        if ($importStat === null) {
            return [];
        }

        return [
            'id' => $importStat->getId(),
            'errorsCount' => $importStat->getCountError(),
            'totalCount' => $importStat->getCountTotal(),
            'loadAverage' => $importStat->getLoadAverage(),
            'isFull' => $importStat->getIsFull(),
            'rowsDeleted' => $importStat->getRowsDeleted(),
            'timeStart' => $this->formatTimestamp((int) $importStat->getTimeStart()),
            'timeEnd' => $this->formatTimestamp((int) $importStat->getTimeEnd()),
        ];
    }

    /**
     * Форматирование временной метки
     * @param int $timestamp
     * @return string
     */
    private function formatTimestamp(int $timestamp): string
    {
        if (!$timestamp) {
            return '';
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

}