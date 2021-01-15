<?php


namespace App\Service\Api;


use App\Repository\RemainsRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Сервис для работы с остатками
 */
class RemainsService
{
    public function __construct(private RemainsRepository $remainsRepository)
    {

    }

    /**
     * Получить остатки со складов
     * @param string $productId
     * @param array $warehouses
     * @return \App\Entity\Remains[]
     */
    public function getRemains(string $productId, array $warehouses = []): array
    {
        $filter = [
            'product_id' => $productId,
        ];
        if (!empty($warehouses)) {
            $filter['warehouse'] = $warehouses;
        }
        $remains = $this->remainsRepository->findBy($filter);
        if (!$remains) {
            throw new NotFoundHttpException('Не найдены данные по остаткам');
        }

        return $remains;
    }
}