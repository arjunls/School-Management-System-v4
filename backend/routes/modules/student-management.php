<?php

use Illuminate\Support\Facades\Route;
use App\Modules\StudentManagement\Student\Controllers\StudentController;
use App\Modules\StudentManagement\Parent\Controllers\ParentController;
use App\Modules\StudentManagement\Attendance\Controllers\AttendanceController;
use App\Modules\StudentManagement\Health\Controllers\HealthController;

// Student Routes
Route::prefix('students')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [StudentController::class, 'getAllStudents']);
    Route::get('/paginated', [StudentController::class, 'getStudentsPaginated']);
    Route::get('/{id}', [StudentController::class, 'getStudent']);
    Route::get('/by-email', [StudentController::class, 'getStudentByEmail']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/', [StudentController::class, 'createStudent']);
        Route::put('/{id}', [StudentController::class, 'updateStudent']);
        Route::delete('/{id}', [StudentController::class, 'deleteStudent']);
    });
});

// Parent routes
Route::prefix('parents')->middleware('auth:sanctum')->group(function () {
    Route::get('/children', [ParentController::class, 'getChildren']);
    Route::post('/link', [ParentController::class, 'linkParentToStudent'])->middleware('role:admin');
    Route::post('/unlink', [ParentController::class, 'unlinkParentFromStudent'])->middleware('role:admin');
    Route::get('/students/{studentId}/parents', [ParentController::class, 'getStudentParents']);
    Route::get('/students/{studentId}/grades', [ParentController::class, 'getStudentGrade']);
});

// Attendance Routes
Route::prefix('attendance')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AttendanceController::class, 'getAllAttendance']);
    Route::get('/paginated', [AttendanceController::class, 'getAttendancePaginated']);
    Route::get('/{id}', [AttendanceController::class, 'getAttendance']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/', [AttendanceController::class, 'createAttendance']);
        Route::put('/{id}', [AttendanceController::class, 'updateAttendance']);
        Route::delete('/{id}', [AttendanceController::class, 'deleteAttendance']);
    });
});

// Health Record routes
Route::prefix('health')->middleware('auth:sanctum')->group(function () {
    Route::get('/{studentId}', [HealthController::class, 'show']);
    Route::put('/{studentId}', [HealthController::class, 'upsert'])->middleware('role:admin,teacher');
});
