<?php

use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::get('/profile', [AuthController::class, 'profile']);

        Route::post('/attendance/scan', [AttendanceController::class, 'scan'])->middleware('throttle:60,1');
        Route::get('/attendance/history', [AttendanceController::class, 'history']);
        Route::get('/attendance/score', [AttendanceController::class, 'score']);
        Route::get('/attendance/active', [AttendanceController::class, 'active']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/token', [NotificationController::class, 'registerToken']);
        Route::delete('/notifications/token', [NotificationController::class, 'removeToken']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    });
});
