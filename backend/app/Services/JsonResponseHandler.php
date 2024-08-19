<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class JsonResponseHandler
{
    private static $instance = null;

    private function __construct()
    {
        // Private constructor to prevent instantiation.
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function success($data, $status = 200, $message = 'Success'): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public function error($message, $status = 400, $data = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $status);
    }
}
