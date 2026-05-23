<?php

namespace App\Kernel\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    protected function success(mixed $data = null, ?string $message = null, int $status = 200, array $extra = []): JsonResponse
    {
        $response = array_merge([
            'success' => true,
            'data' => $data,
        ], $extra);

        if ($message !== null) {
            $response['message'] = $message;
        }

        return response()->json($response, $status);
    }

    protected function created(mixed $data = null, ?string $message = null): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function error(string $message = 'Internal server error', int $status = 500, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 404);
    }

    protected function paginated(LengthAwarePaginator $paginator, ?string $message = null): JsonResponse
    {
        $extra = [
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];

        return $this->success($paginator->items(), $message, 200, $extra);
    }

    protected function deleted(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return $this->success(null, $message);
    }
}
