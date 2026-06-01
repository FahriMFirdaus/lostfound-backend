<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    
    // Rute Publik (Mading Digital)
    Route::get('/items', [ItemController::class, 'index']);

    // Rute yang butuh Token JWT/Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        
        // Rute Laporan Barang
        Route::post('/items', [ItemController::class, 'store']);
        Route::patch('/items/{id}/release', [ItemController::class, 'release']);
    });
});
