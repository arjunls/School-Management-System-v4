<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Reporting\Report\Controllers\ReportController;
use App\Modules\Reporting\Export\Controllers\ExportController;
use App\Modules\Reporting\Import\Controllers\ImportController;

// Report routes
Route::prefix('reports')->middleware('auth:sanctum')->group(function () {
    Route::get('/student-report-card/{studentId}', [ReportController::class, 'studentReportCard']);
    Route::get('/attendance', [ReportController::class, 'attendanceReport']);
    Route::get('/transcript/{studentId}', [ReportController::class, 'transcript']);
});

// Export Routes (CSV downloads)
Route::prefix('export')->middleware('auth:sanctum')->group(function () {
    Route::get('/students', [ExportController::class, 'studentsCSV']);
    Route::get('/teachers', [ExportController::class, 'teachersCSV']);
    Route::get('/classes', [ExportController::class, 'classesCSV']);
    Route::get('/subjects', [ExportController::class, 'subjectsCSV']);
    Route::get('/grades', [ExportController::class, 'gradesCSV']);
    Route::get('/attendance', [ExportController::class, 'attendanceCSV']);
});

// Import Routes (CSV upload)
Route::prefix('import')->middleware('auth:sanctum')->group(function () {
    Route::post('/students', [ImportController::class, 'importStudents']);
    Route::post('/teachers', [ImportController::class, 'importTeachers']);
});
