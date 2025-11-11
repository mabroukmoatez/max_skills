<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\JsonResponse;


Route::middleware('auth:sanctum')->group(function () {
    // Get user notifications
    Route::get('/notifications', [NotificationController::class, 'getNotifications']);
    
    // Mark notifications as read
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
    
    // Check for new notifications (count only)
    Route::get('/notifications/check-new', [NotificationController::class, 'checkNewNotifications']);
});
