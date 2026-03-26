<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Standard success response.
     *
     * @param  mixed  $data
     * @param  string $message
     * @param  int    $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, string $message = 'Operation successful', int $statusCode = 200, array $meta = null): JsonResponse
    {
        $response = [
            'success'   => true,
            'message'   => $message,
            'data'      => $data,
        ];

        // If the meta data is provided, add it to the response.
        if ($meta !== null) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Standard error response.
     *
     * @param  string $message
     * @param  int    $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success'   => false,
            'message'   => $message,
            'data'      => [],
        ], $statusCode);
    }
}