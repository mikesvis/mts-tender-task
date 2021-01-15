<?php


namespace App\Service\Api;


use App\Entity\ImportStat;
use App\Repository\ImportStatRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Сервис для работы с ImportStat
 */
class ImportStatService
{
    public function __construct(private ImportStatRepository $importStatRepository)
    {

    }

    /**
     * Получить последнюю запись статистики
     *
     * @return ImportStat
     */
    public function getLast(): ImportStat
    {
        $importStat = $this->importStatRepository->findOneBy([], ['id' => 'desc']);
        if (!$importStat) {
            throw new NotFoundHttpException('Не найдены записи статистики');
        }

        return $importStat;
    }

    /**
     * Получить конкретную запись статистики по id
     *
     * @param int $id Идентификатор записи
     * @return ImportStat
     */
    public function getById(int $id): ImportStat
    {
        $importStat = $this->importStatRepository->find($id);
        if (!$importStat) {
            throw new NotFoundHttpException('Не найдены записи статистики');
        }

        return $importStat;
    }
}