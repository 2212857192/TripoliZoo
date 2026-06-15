<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthPasswordController::class, 'sendCode']);
    Route::post('/verify-otp', [AuthPasswordController::class, 'verifyCode']);
    Route::post('/reset-password', [AuthPasswordController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
