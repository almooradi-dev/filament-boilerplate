<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class APIController extends Controller
{
    /**
     * Send success JSON response
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public function sendResponse(mixed $data = [], string $message = ''): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message
        ];

        return response()->json($response, 200);
    }

    /**
     * Send error JSON response
     *
     * @param string $error
     * @param array $errorMessages
     * @param integer $code
     * @return JsonResponse
     */
    public function sendError(string $message, string|array $errors = [], $code = 500, $key = ''): JsonResponse
    {
        // TODO: Store error to log file
        
        $errors = is_array($errors) ? $errors : [$errors];

        $response = [
            'message' => $message,
            'errors' => $errors,
            'code' => $code,
            'key' => $key,
        ];

        return response()->json($response, $code);
    }
}