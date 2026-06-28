<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\HandoverController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    
    // Rute Publik (Mading Digital)
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{id}', [ItemController::class, 'show']);

    // Rute yang butuh Token JWT/Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        
        // Rute Manajemen User
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
        Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);

        // Rute Lokasi
        Route::get('/locations', [\App\Http\Controllers\LocationController::class, 'index']);
        Route::post('/locations', [\App\Http\Controllers\LocationController::class, 'store']);
        Route::delete('/locations/{id}', [\App\Http\Controllers\LocationController::class, 'destroy']);

        // Rute Laporan Barang
        Route::get('/admin/items', [ItemController::class, 'adminIndex']);
        Route::post('/items', [ItemController::class, 'store']);
        Route::patch('/items/{id}/release', [ItemController::class, 'release']);
        Route::patch('/items/{id}/reject', [ItemController::class, 'reject']);
        
        // Rute Klaim Barang (Fase 4)
        Route::get('/claims', [ClaimController::class, 'index']);
        Route::get('/claims/my-claims', [ClaimController::class, 'myClaims']);
        Route::post('/claims', [ClaimController::class, 'store']);
        Route::get('/claims/{id}', [ClaimController::class, 'show']);
        Route::patch('/claims/{id}/verify', [ClaimController::class, 'verify']);
        
        // Rute Serah Terima & Arsip (Fase 5)
        Route::post('/handovers', [HandoverController::class, 'store']);
        Route::get('/handovers/{id}', [HandoverController::class, 'show']);
    });
});
