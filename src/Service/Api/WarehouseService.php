<?php


namespace App\Service\Api;


use App\Entity\Region;
use App\Repository\CityRepository;
use App\Repository\RegionRepository;
use App\Repository\WarehouseRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Сервис для работы со складами
 */
class WarehouseService
{
    public function __construct(
        private WarehouseRepository $warehouseRepository,
        private CityRepository $cityRepository,
        private RegionRepository $regionRepository
    )
    {

    }

    /**
     * Получить склады города
     *
     * @param int $cityId
     * @return \App\Entity\Warehouse[]
     */
    public function getByCity(int $cityId): array
    {
        $city = $this->cityRepository->findOneBy(['id' => $cityId]);

        if (!$city) {
            throw new NotFoundHttpException(sprintf('Город %d не найден', $cityId));
        }

        return $this->getByFilter([
            'city' => $city
        ]);
    }

    /**
     * Получить склады региона
     *
     * @param int $cityId
     * @return \App\Entity\Warehouse[]
     */
    public function getByRegion(int $regionId): array
    {
        /** @var Region $region */
        $region = $this->regionRepository->findOneBy(['id' => $regionId]);

        if (!$region) {
            throw new NotFoundHttpException(sprintf('Регион %d не найден', $regionId));
        }

        $cities = $region->getCities()->toArray();

        if (empty($cities)) {
            throw new NotFoundHttpException(sprintf('У региона %d нет привязанных городов', $regionId));
        }

        return $this->getByFilter([
            'city' => $cities
        ]);
    }

    /**
     * Получить склад по ID
     *
     * @param string $warehouseId
     * @return \App\Entity\Warehouse[]
     */
    public function getById(string $warehouseId): array
    {
        return $this->getByFilter([
            'id' => $warehouseId
        ]);
    }

    /**
     * Общая логика для выборок по складам
     *
     * @param array $filter Условия фильтрации
     * @return \App\Entity\Warehouse[]
     */
    private function getByFilter(array $filter): array
    {
        if (empty($filter)) {
            throw new \RuntimeException('Не заданы условия фильтрации для складов');
        }

        $warehouses = $this->warehouseRepository->findBy($filter);
        if (!$warehouses) {
            throw new NotFoundHttpException('Склады не найдены');
        }
        return $warehouses;
    }
}