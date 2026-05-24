<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Communication\Announcement\Controllers\AnnouncementController;
use App\Modules\Communication\Message\Controllers\MessageController;
use App\Modules\Communication\Notification\Controllers\NotificationController;

// Announcement routes
Route::prefix('announcements')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index']);
    Route::post('/', [AnnouncementController::class, 'store'])->middleware('role:admin,teacher');
    Route::put('/{id}', [AnnouncementController::class, 'update'])->middleware('role:admin,teacher');
    Route::delete('/{id}', [AnnouncementController::class, 'destroy'])->middleware('role:admin');
});

// Message routes
Route::prefix('messages')->middleware('auth:sanctum')->group(function () {
    Route::get('/conversations', [MessageController::class, 'conversations']);
    Route::post('/conversations', [MessageController::class, 'createConversation']);
    Route::get('/conversations/{conversationId}', [MessageController::class, 'messages']);
    Route::post('/send', [MessageController::class, 'send']);
});

// Notification routes
Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread', [NotificationController::class, 'unread']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});
