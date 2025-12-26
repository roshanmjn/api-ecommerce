<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    /**
     * Return a successful response with data.
     */
    public function successResponse(array|object $data = [], int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * Return success message and status only.
     */
    public function successMessage(string $message, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message'    => $message,
        ], $statusCode);
    }

    /**
     * Return a successful response with message and data.
     */
    public function successResponseWithMessage(array|object $data = [], string $message = '', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * Return a paginated response.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     */
    public function successResponsePaginated($paginator, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
            'data'    => $paginator->items(),
        ], $statusCode);
    }

    /**
     * Return an error response.
     */
    public function errorResponse(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
