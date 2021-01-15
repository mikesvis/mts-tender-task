<?php


namespace App\Util\Api;


use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Класс для формирования http-ответа
 */
class ResponseHelper
{
    /**
     * Метод контроллера отработал успешно
     * @param array $data
     * @return JsonResponse
     */
    public function getSuccessResponse(array $data): JsonResponse
    {
        return new JsonResponse([
            'success' => 'true',
            'data' => $data
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Метод контроллера отработал с ошибкой
     * @param string $message
     * @return JsonResponse
     */
    public function getFailureResponse(string $message): JsonResponse
    {
        return new JsonResponse([
            'success' => 'false',
            'error' => $message
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}