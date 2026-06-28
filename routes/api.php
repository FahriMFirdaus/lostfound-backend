<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\HandoverController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    
    // Rute Publik (Mading Digital)
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{id}', [ItemController::class, 'show']);

    // Rute yang butuh Token JWT/Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::get('/auth/stats', [AuthController::class, 'stats']);
        Route::get('/auth/riwayat', [AuthController::class, 'riwayat']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        
        // User Management (Admin)
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store']);
        Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update']);
        Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);

        // Rute Lokasi & Kategori
        Route::get('/locations', [\App\Http\Controllers\LocationController::class, 'index']);
        Route::post('/locations', [\App\Http\Controllers\LocationController::class, 'store']);
        Route::delete('/locations/{id}', [\App\Http\Controllers\LocationController::class, 'destroy']);
        Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index']);

        // Rute Laporan Barang
        Route::get('/admin/items', [ItemController::class, 'adminIndex']);
        Route::post('/items', [ItemController::class, 'store']);
        Route::patch('/items/{id}/release', [ItemController::class, 'release']);
        Route::patch('/items/{id}/reject', [ItemController::class, 'reject']);
        
        // Rute Klaim Barang (Fase 4)
        Route::get('/claims', [ClaimController::class, 'index']);
        Route::get('/claims/my-claims', [ClaimController::class, 'myClaims']);
        Route::post('/claims', [ClaimController::class, 'store']);
        Route::get('/claims/token/{token}', [ClaimController::class, 'getByToken']);
        Route::get('/claims/{id}', [ClaimController::class, 'show']);
        Route::patch('/claims/{id}/verify', [ClaimController::class, 'verify']);
        
        // Rute Serah Terima & Arsip (Fase 5)
        Route::post('/handovers', [HandoverController::class, 'store']);
        Route::get('/handovers/{id}', [HandoverController::class, 'show']);
    });
});
