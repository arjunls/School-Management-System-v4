<?php

use Illuminate\Support\Facades\Route;
use App\Modules\StudentLife\Extracurricular\Controllers\ExtracurricularController;

// Extracurricular routes
Route::prefix('extracurriculars')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ExtracurricularController::class, 'index']);
    Route::post('/', [ExtracurricularController::class, 'store'])->middleware('role:admin,teacher');
    Route::put('/{id}', [ExtracurricularController::class, 'update'])->middleware('role:admin,teacher');
    Route::delete('/{id}', [ExtracurricularController::class, 'destroy'])->middleware('role:admin');
    Route::post('/{id}/join', [ExtracurricularController::class, 'join'])->middleware('role:student');
    Route::post('/{id}/leave', [ExtracurricularController::class, 'leave'])->middleware('role:student');
});
