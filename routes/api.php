<?php

use App\Http\Controllers\ResellerController;
use App\Http\Controllers\UserSalesController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::post('/auth/register', [UserSalesController::class, 'register']);
Route::post('/auth/login', [UserSalesController::class, 'login']);
Route::post('/auth/verify-phone', [UserSalesController::class, 'verifyPhone']);

// Route untuk CRUD Reseller
Route::middleware('auth:sanctum')->group(function () {
    // Reseller Routes
    Route::post('/resellers', [ResellerController::class, 'store']);       // Create reseller
    Route::get('/resellers', [ResellerController::class, 'index']);        // List semua reseller
    Route::get('/resellers/{id}', [ResellerController::class, 'show']);    // Detail reseller berdasarkan ID
    Route::put('/resellers/{id}', [ResellerController::class, 'update']);
    Route::patch('/resellers/{id}/status', [ResellerController::class, 'updateStatus']); // Update reseller berdasarkan ID
    Route::delete('/resellers/{id}', [ResellerController::class, 'destroy']); // Hapus reseller

    // Task Routes
    Route::post('/tasks', [TaskController::class, 'store']); // Create task
    Route::get('/tasks', [TaskController::class, 'index']); // List tasks
    Route::put('/tasks/{id}', [TaskController::class, 'update']); // Update task
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']); // Delete task
    Route::put('/tasks/{id}/mark-as-completed', [TaskController::class, 'markAsCompleted']); // Mark task as completed

    // UserSales Routes
    Route::get('/usersales', [UserSalesController::class, 'index']);       // List semua user sales
    Route::get('/usersales/{id}', [UserSalesController::class, 'show']);    // Detail user sales berdasarkan ID
    Route::put('/usersales/{id}', [UserSalesController::class, 'update']);  // Update user sales
    Route::put('/usersales/{id}/update-profile-picture', [UserSalesController::class, 'updateProfilePicture']); // Update profile picture
    Route::delete('/usersales/{id}', [UserSalesController::class, 'destroy']); // Hapus user sales

    // Logout
    Route::post('/auth/logout', [UserSalesController::class, 'logout']);
});