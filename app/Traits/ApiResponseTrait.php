<?php

namespace App\Traits;

use App\DTOs\ServiceResult;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return a standardized JSON response.
     */
    protected function jsonResponse(
        bool $success,
        string $message,
        mixed $data = null,
        int $statusCode = 200,
        ?array $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Map a ServiceResult directly to a JsonResponse.
     */
    protected function serviceResponse(ServiceResult $result): JsonResponse
    {
        return $this->jsonResponse(
            $result->success,
            $result->message,
            $result->data,
            $result->statusCode,
            $result->errors
        );
    }
}
