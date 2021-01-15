<?php

namespace App\Service\Statistics;

use App\Entity\ImportStat;
use App\Repository\ImportStatRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Сервис для статистики импорта
 * @package App\Service
 */
class StatisticsService
{
    private ImportStatRepository $importStatRepository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->importStatRepository = $this->entityManager->getRepository(ImportStat::class);
    }

    /**
     * Возвращает последнюю запись статистики
     *
     * @throws \Exception
     */
    public function getLast(): ImportStat
    {
        $importStat = $this->importStatRepository->findOneBy(array(),array('time_start'=>'DESC'),1,0);
        if (!$importStat) {
            throw new \Exception('Последняя запись статистики на найдена');
        }
        return $importStat;
    }

    /**
     * Возвращает запись статистики по id
     *
     * @param integer $id
     * @return ImportStat
     * @throws \Exception
     */
    public function getById(int $id): ImportStat
    {
        $importStat = $this->importStatRepository->findOneBy(array('time_start' => $id));
        if (!$importStat) {
            throw new \Exception('Запись статистики на найдена');
        }
        return $importStat;
    }

    /**
     * Создает запись в статистике
     *
     * @param int $id
     * @param int $type
     * @return ImportStat
     * @throws \Exception
     */
    public function create(int $id, int $type): ImportStat
    {
        try {
            $importStat = new ImportStat();
            $importStat->setTimeStart($id);
            $importStat->setIsFull($type);
            $this->entityManager->persist($importStat);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Ошибка добавления статистики: %s', $e->getMessage()));
        }
        return $importStat;
    }

    /**
     * Добавляет результаты обработки в статистику
     *
     * @param int $id
     * @param int $total
     * @param int $error
     * @return ImportStat
     * @throws \Exception
     */
    public function addCount(int $id, int $total, int $error): ImportStat
    {
        try {
            $importStat = $this->getById($id);
            $importStat->setCountTotal($importStat->getCountTotal() + $total);
            $importStat->setCountError($importStat->getCountError() + $error);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Ошибка добавления счетчиков в статистику: %s', $e->getMessage()));
        }
        return $importStat;
    }

    /**
     * @param int $id
     * @param int $time_end
     * @param float $la
     * @param int $rows_deleted
     * @return ImportStat
     * @throws \Exception
     */
    public function finish(int $id, int $time_end, float $la, int $rows_deleted): ImportStat
    {
        try {
            $importStat = $this->getById($id);
            $importStat->setTimeEnd($time_end);
            $importStat->setLoadAverage($la);
            $importStat->setRowsDeleted($rows_deleted);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Ошибка обновления статистики: %s', $e->getMessage()));
        }
        return $importStat;
    }
}