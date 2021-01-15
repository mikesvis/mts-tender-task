<?php


namespace LocationImportBundle\Service;


use App\Entity\Warehouse;
use App\Repository\CityRepository;
use App\Repository\WarehouseRepository;

/**
 * Сервис для работы с данными по складам
 */
class ServiceWarehouse
{
    /** @var string Первая буква кода склада */
    const STORE_LETTER = 'C';

    /** @var int Количество складов */
    const STORE_MAX = 1000;

    /**
     * ServiceWarehouse конструктор
     *
     * @param WarehouseRepository $warehouseRepository Реползиторий для работы с сущностями из таблицы скадов
     * @param CityRepository $cityRepository Реползиторий для работы с сущностями из таблицы городов
     */
    public function __construct(
        private WarehouseRepository $warehouseRepository,
        private CityRepository $cityRepository
    )
    {
    }

    /**
     * Удалить все склады
     */
    public function truncate(): void
    {
        $this->warehouseRepository->truncate();
    }

    /**
     * Заполнить данные по складам
     */
    public function fillData(): void
    {
        $entities = $this->getEntities();
        $this->warehouseRepository->insert($entities);
    }

    /**
     * Получить массив сущностей
     *
     * @return array
     * @throws \Exception
     */
    protected function getEntities(): array
    {
        $cities = $this->getCities();
        $warehouses = [];
        for ($i = 0; $i < self::STORE_MAX; $i ++) {
            $storeCode = sprintf('%s%03d', self::STORE_LETTER, $i);
            $warehouse = new Warehouse();
            $warehouse->setId($storeCode);
            $warehouse->setCity($cities[random_int(0, (count($cities) - 1))]);
            $warehouses[] = $warehouse;
        }
        return $warehouses;
    }

    /**
     * Получить все сохраненные города
     *
     * @return array
     */
    protected function getCities(): array
    {
        $cities = $this->cityRepository->findAll();
        if (empty($cities)) {
            throw new \RuntimeException("Не заполнены данные по городам");
        }

        return $cities;
    }

}