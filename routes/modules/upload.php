<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Upload\Controllers\UploadController;

// Upload routes
Route::prefix('upload')->middleware('auth:sanctum')->group(function () {
    Route::post('/photo', [UploadController::class, 'uploadPhoto']);
    Route::post('/document', [UploadController::class, 'uploadDocument']);
});
