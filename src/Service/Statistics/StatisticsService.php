<?php

namespace App\Service\Statistics;

use App\Entity\ImportStat;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Сервис для статистики импорта
 * @package App\Service
 */
class StatisticsService
{
    private \Doctrine\Persistence\ObjectRepository $importStatRepository;

    /**
     * StatisticsService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param Connection $dbConnection
     */
    public function __construct(private EntityManagerInterface $entityManager, private Connection $dbConnection)
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
            $this->entityManager->clear();
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
     * @return void
     */
    public function addCount(int $id, int $total, int $error): void
    {
        $this->dbConnection->exec(
            'UPDATE `import_stat` SET 
                    `count_total` = `count_total` + ' . $total . ', 
                    `count_error` = `count_error` + ' . $error . '
                    WHERE `time_start` = ' . $id
        );
    }

    /**
     * @param int $id
     * @param int $timeEnd
     * @param float $la
     * @param int $rowsDeleted
     * @return ImportStat
     * @throws \Exception
     */
    public function finish(int $id, int $timeEnd, float $la, int $rowsDeleted): ImportStat
    {
        try {
            $importStat = $this->getById($id);
            $importStat->setTimeEnd($timeEnd);
            $importStat->setLoadAverage($la);
            $importStat->setRowsDeleted($rowsDeleted);
            $this->entityManager->flush();
            $this->entityManager->clear();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Ошибка обновления статистики: %s', $e->getMessage()));
        }
        return $importStat;
    }
}