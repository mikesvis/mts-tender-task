<?php


namespace App\Formatter\Api;


use App\Entity\Remains;

/**
 * Класс для форматирования остатков в массив для передачи в JsonResponce
 */
class RemainsFormatter
{
    /**
     * Форматирование сущности в массив
     * @param array $remains
     * @return array
     */
    public function formatRemainsToResponse(array $remains): array
    {
        if (empty($remains)) {
            return [];
        }

        $stores = [];
        /** @var Remains $remain */
        foreach ($remains as $k => $remain) {
            $storeId = $remain?->getWarehouse()?->getId();

            if (!$storeId) {
                throw new \RuntimeException(sprintf('Не найден склад товара %d', $remain->getProductId()));
            }

            if (!isset($stores[$storeId])) {
                $stores[$storeId] = [];
            }

            $stores[$storeId][] = [
                'product_id' => $remain->getProductId(),
                'quantity' => $remain->getCount()
            ];
        }

        return ['stores' => $stores];
    }
}