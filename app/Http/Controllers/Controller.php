<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function successResponse($message = "", $data = [], $code = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    protected function errorResponse($message = "", $errors = [], $code = 404): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors'  => $errors
        ], $code);
    }
}
