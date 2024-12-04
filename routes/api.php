<?php

use App\Http\Controllers\ResellerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/verify-phone', [AuthController::class, 'verifyPhone']);

// Route untuk CRUD Reseller
Route::middleware('auth:sanctum')->group(function () {
    // Reseller Routes
    Route::post('/resellers', [ResellerController::class, 'store']);       // Create reseller
    Route::get('/resellers', [ResellerController::class, 'index']);        // List semua reseller
    Route::get('/resellers/{id}', [ResellerController::class, 'show']);    // Detail reseller berdasarkan ID
    Route::put('/resellers/{id}', [ResellerController::class, 'update']);  // Update reseller berdasarkan ID
    Route::delete('/resellers/{id}', [ResellerController::class, 'destroy']); // Hapus reseller

    // Visit Routes
    Route::post('/visits', [VisitController::class, 'store']);              // Membuat visit baru
    Route::post('/visits/confirm/{visit_id}', [VisitController::class, 'confirm']); // Konfirmasi visit

    Route::get('/tasks/{resellerId}', [TaskController::class, 'index']);       // List task untuk reseller tertentu
    Route::post('/tasks', [TaskController::class, 'store']);                  // Tambah task baru
    Route::get('/tasks/detail/{id}', [TaskController::class, 'show']);        // Detail task
    Route::put('/tasks/{id}', [TaskController::class, 'update']);             // Update task
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);         // Hapus task
});



