<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;

Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
    });

    // Set Up Pusher Authentication Endpoint
    Route::post('/pusher/auth', function (Request $request) {
        $pusher = new \Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            ['cluster' => env('PUSHER_APP_CLUSTER'), 'useTLS' => true]
        );
        return $pusher->socket_auth($request->channel_name, $request->socket_id);
    })->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function() {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::apiResource('tickets', TicketController::class);
        Route::apiResource('comments', CommentController::class)->except(['index', 'show']);
        Route::apiResource('chats', ChatController::class)->except(['index', 'show']);
    });
});
