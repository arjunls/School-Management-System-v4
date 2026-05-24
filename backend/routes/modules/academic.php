<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Academic\AcademicYear\Controllers\AcademicYearController;
use App\Modules\Academic\Class\Controllers\ClassController;
use App\Modules\Academic\Subject\Controllers\SubjectController;
use App\Modules\Academic\Schedule\Controllers\ScheduleController;

// Academic Years & Terms (admin only)
Route::prefix('academic-years')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [AcademicYearController::class, 'getAll']);
    Route::get('/paginated', [AcademicYearController::class, 'paginate']);
    Route::get('/active', [AcademicYearController::class, 'getActive']);
    Route::get('/{id}', [AcademicYearController::class, 'find']);
    Route::post('/', [AcademicYearController::class, 'create']);
    Route::put('/{id}', [AcademicYearController::class, 'update']);
    Route::delete('/{id}', [AcademicYearController::class, 'delete']);

    Route::get('/{academicYearId}/terms', [AcademicYearController::class, 'getTerms']);
    Route::post('/{academicYearId}/terms', [AcademicYearController::class, 'createTerm']);
    Route::put('/terms/{id}', [AcademicYearController::class, 'updateTerm']);
    Route::delete('/terms/{id}', [AcademicYearController::class, 'deleteTerm']);
});

// Class Routes
Route::prefix('classes')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ClassController::class, 'getAllClasses']);
    Route::get('/paginated', [ClassController::class, 'getClassesPaginated']);
    Route::get('/{id}', [ClassController::class, 'getClass']);
    Route::get("/{classId}/students", [ClassController::class, "getClassStudents"]);

    Route::middleware('role:admin')->group(function () {
        Route::post('/', [ClassController::class, 'createClass']);
        Route::put('/{id}', [ClassController::class, 'updateClass']);
        Route::delete('/{id}', [ClassController::class, 'deleteClass']);
        Route::post("/{classId}/students/{studentId}", [ClassController::class, "addStudentToClass"]);
        Route::delete("/{classId}/students/{studentId}", [ClassController::class, "removeStudentFromClass"]);
    });
});

// Subject Routes
Route::prefix('subjects')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [SubjectController::class, 'getAllSubjects']);
    Route::get('/paginated', [SubjectController::class, 'getSubjectsPaginated']);
    Route::get('/{id}', [SubjectController::class, 'getSubject']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/', [SubjectController::class, 'createSubject']);
        Route::put('/{id}', [SubjectController::class, 'updateSubject']);
        Route::delete('/{id}', [SubjectController::class, 'deleteSubject']);
    });
});

// Schedule Routes
Route::prefix('schedules')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ScheduleController::class, 'getAllSchedules']);
    Route::get('/paginated', [ScheduleController::class, 'getSchedulesPaginated']);
    Route::get('/{id}', [ScheduleController::class, 'getSchedule']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/', [ScheduleController::class, 'createSchedule']);
        Route::put('/{id}', [ScheduleController::class, 'updateSchedule']);
        Route::delete('/{id}', [ScheduleController::class, 'deleteSchedule']);
    });
});
