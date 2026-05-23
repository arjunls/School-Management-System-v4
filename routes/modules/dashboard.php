<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\Controllers\DashboardController;

Route::prefix('dashboard')->middleware('auth:sanctum')->group(function () {
    Route::get('/stats', [DashboardController::class, 'getStats']);
    Route::get('/attendance-chart', [DashboardController::class, 'getAttendanceChartData']);
    Route::get('/performance-chart', [DashboardController::class, 'getPerformanceChartData']);
    Route::get('/student-performance/{studentId}', [DashboardController::class, 'getStudentPerformanceTrend']);
});
