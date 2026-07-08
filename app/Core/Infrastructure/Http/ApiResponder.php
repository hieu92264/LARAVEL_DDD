<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Http;

use Illuminate\Http\JsonResponse;

final class ApiResponder
{
    public function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        ?array $meta = null
    ): JsonResponse
    {
        return $this->respond([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => null,
        ], $status);
    }

    public function created(
        mixed $data = null,
        string $message = 'Created Successfully',
        int $status = 201,
    ): JsonResponse {
        return $this->success(
            data: $data,
            message: $message,
            status: $status
        );
    }

    public function error(
        string $message,
        int $status,
        string $errorCode,
        ?array $errors = null,
    ): JsonResponse {
        return $this->respond([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errorCode' => $errorCode,
            'errors' => $errors,
        ], $status);
    }

    private function respond(
        array $payload,
        int $status
    ): JsonResponse {
        $requestId = request()->header('X-Request-Id');

        $payload['requestId'] = $requestId;
        $payload['path'] = request()->getPathInfo();
        $payload['timestamp'] = now()->toISOString();

        $response = response()->json($payload, $status);

        if(is_string($requestId)) {
            $response->headers->set('X-Request-Id', $requestId);
        }

        $response->headers->set(
            'Content-Language',
            app()->currentLocale()
        );

        return $response;
    }
}
