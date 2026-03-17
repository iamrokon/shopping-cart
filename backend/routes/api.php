<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// Product routes (public)
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show'])->where('id', '[0-9]+');
});

// Cart routes (authenticated only)
Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/', [CartController::class, 'store']);
    Route::delete('/', [CartController::class, 'clear']);
    Route::post('/batch-sync', [CartController::class, 'batchSync']);
    Route::patch('/{id}', [CartController::class, 'update'])->where('id', '[0-9]+');
    Route::post('/{id}/increment', [CartController::class, 'increment'])->where('id', '[0-9]+');
    Route::post('/{id}/decrement', [CartController::class, 'decrement'])->where('id', '[0-9]+');
    Route::delete('/{id}', [CartController::class, 'destroy'])->where('id', '[0-9]+');
});
