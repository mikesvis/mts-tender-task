<?php


namespace App\Controller\Api;


use App\Formatter\Api\ImportStatFormatter;
use App\Service\Api\ImportStatService;
use App\Util\Api\ResponseHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/import-stat")
 */
class ImportStatController extends AbstractController
{
    /**
     * ImportStatController constructor.
     */
    public function __construct(
        private ImportStatService $importStatService,
        private ImportStatFormatter $importStatFormatter,
        private ResponseHelper $responseHelper
    )
    {
    }

    /**
     * Получени последней записи статистики
     * @Route("/", methods={"GET"})
     * @return JsonResponse
     */
    public function getLast(): JsonResponse
    {
        try {
            $importStatRow = $this->importStatService->getLast();
            $data = $this->importStatFormatter->formatImportStatToResponse($importStatRow);
            $response = $this->responseHelper->getSuccessResponse($data);
        } catch (\Exception $e) {
            $response = $this->responseHelper->getFailureResponse($e->getMessage());
        }

        return $response;
    }

    /**
     * Получение записи статистики по id
     * @Route("/{id}", methods={"GET"})
     * @return JsonResponse
     */
    public function getById(int $id): JsonResponse
    {
        try {
            $importStatRow = $this->importStatService->getById($id);
            $data = $this->importStatFormatter->formatImportStatToResponse($importStatRow);
            $response = $this->responseHelper->getSuccessResponse($data);
        } catch (\Exception $e) {
            $response = $this->responseHelper->getFailureResponse($e->getMessage());
        }

        return $response;
    }
}