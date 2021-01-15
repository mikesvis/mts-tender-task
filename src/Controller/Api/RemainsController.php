<?php


namespace App\Controller\Api;


use App\Formatter\Api\RemainsFormatter;
use App\Service\Api\RemainsService;
use App\Service\Api\WarehouseService;
use App\Util\Api\ResponseHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/remains")
 */
class RemainsController extends AbstractController
{
    /**
     * RemainsController конструктор
     *
     * @param WarehouseService $warehouseService Сервис для работы с данными по складам
     * @param RemainsService $remainsService Сервис для работы сданными по остаткам
     * @param RemainsFormatter $remainsFormatter Сервис для форматирования данных на вывод
     * @param ResponseHelper $responseHelper  Сервис для формирования ответа сервера
     */
    public function __construct(
        private WarehouseService $warehouseService,
        private RemainsService $remainsService,
        private RemainsFormatter $remainsFormatter,
        private ResponseHelper $responseHelper
    )
    {
    }

    /**
     * Получить все остатки конкретного товара
     *
     * @return JsonResponse
     * @Route("/{productId}", methods={"GET"})
     */
    public function getRemainsById(string $productId): JsonResponse
    {
        $productId = htmlentities($productId);
        return $this->getRemainsResponse($productId);
    }

    /**
     * Получить все остатки товара на складе
     *
     * @param string $productId Артикул товара
     * @param string $warehouseId Код склада
     * @return JsonResponse
     * @Route("/{productId}/warehouse/{warehouseId}", methods={"GET"})
     */
    public function getByWarehouse(string $productId, string $warehouseId): JsonResponse
    {
        $productId = htmlentities($productId);
        $warehouseId = htmlentities($warehouseId);
        try {
            $warehouses = $this->warehouseService->getById($warehouseId);
        } catch (\Exception $e) {
            return $this->responseHelper->getFailureResponse($e->getMessage());
        }

        return $this->getRemainsResponse($productId, $warehouses);
    }

    /**
     * Получить все остатки товара в регионе
     *
     * @param string $productId Артикул товара
     * @param int $regionId ID региона
     * @return JsonResponse
     * @Route("/{productId}/region/{regionId}", methods={"GET"})
     */
    public function getByRegion(string $productId, int $regionId): JsonResponse
    {
        $productId = htmlentities($productId);
        try {
            $warehouses = $this->warehouseService->getByRegion($regionId);
        } catch (\Exception $e) {
            return $this->responseHelper->getFailureResponse($e->getMessage());
        }

        return $this->getRemainsResponse($productId, $warehouses);
    }

    /**
     * Получить все остатки товара в городе
     *
     * @param string $productId Артикул товара
     * @param int $cityId Id города
     * @return JsonResponse
     * @Route("/{productId}/city/{cityId}", methods={"GET"})
     */
    public function getByCity(string $productId, int $cityId): JsonResponse
    {
        $productId = htmlentities($productId);
        try {
            $warehouses = $this->warehouseService->getByCity($cityId);
        } catch (\Exception $e) {
            return $this->responseHelper->getFailureResponse($e->getMessage());
        }

        return $this->getRemainsResponse($productId, $warehouses);
    }

    /**
     * Получить отформатированный ответ
     *
     * @return JsonResponse
     */
    private function getRemainsResponse($productId, $warehouses = []): JsonResponse
    {
        $productId = htmlentities($productId);
        try {
            $remains = $this->remainsService->getRemains($productId, $warehouses);
            $remainsResponseData = $this->remainsFormatter->formatRemainsToResponse($remains);
            $response = $this->responseHelper->getSuccessResponse($remainsResponseData);
        } catch (\Exception $e) {
            $response = $this->responseHelper->getFailureResponse($e->getMessage());
        }

        return $response;
    }
}