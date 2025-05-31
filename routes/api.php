<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PlatformController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User profile routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user', [AuthController::class, 'updateProfile']);

    // Post routes
    Route::apiResource('posts', PostController::class)->except(['store']);
    Route::post('posts', [PostController::class,'store'])->middleware(\App\Http\Middleware\PostRateLimit::class);

    // Platform routes
    Route::apiResource('platforms', PlatformController::class)->only(['index']);
    Route::post('platforms/{platform}/toggle', [PlatformController::class, 'toggleActive']);
    
}); 