<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Learning\Assignment\Controllers\AssignmentController;
use App\Modules\Learning\Quiz\Controllers\QuizController;
use App\Modules\Learning\ExamSchedule\Controllers\ExamScheduleController;
use App\Modules\Learning\Grade\Controllers\GradeController;

// Grade Routes
Route::prefix('grades')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [GradeController::class, 'getAllGrades']);
    Route::get('/paginated', [GradeController::class, 'getGradesPaginated']);
    Route::get('/{id}', [GradeController::class, 'getGrade']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/', [GradeController::class, 'createGrade']);
        Route::put('/{id}', [GradeController::class, 'updateGrade']);
        Route::delete('/{id}', [GradeController::class, 'deleteGrade']);
    });
});

// Assignment routes
Route::prefix('assignments')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AssignmentController::class, 'index']);
    Route::post('/', [AssignmentController::class, 'store'])->middleware('role:admin,teacher');
    Route::get('/{id}', [AssignmentController::class, 'show']);
    Route::put('/{id}', [AssignmentController::class, 'update'])->middleware('role:admin,teacher');
    Route::delete('/{id}', [AssignmentController::class, 'destroy'])->middleware('role:admin,teacher');
    Route::post('/{id}/submit', [AssignmentController::class, 'submit'])->middleware('role:student');
    Route::post('/{id}/submissions/{submissionId}/grade', [AssignmentController::class, 'grade'])->middleware('role:admin,teacher');
});

// Exam Schedule routes
Route::prefix('exam-schedules')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ExamScheduleController::class, 'index']);
    Route::post('/', [ExamScheduleController::class, 'store'])->middleware('role:admin,teacher');
    Route::put('/{id}', [ExamScheduleController::class, 'update'])->middleware('role:admin,teacher');
    Route::delete('/{id}', [ExamScheduleController::class, 'destroy'])->middleware('role:admin,teacher');
});

// Quiz routes
Route::prefix('quizzes')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [QuizController::class, 'index']);
    Route::post('/', [QuizController::class, 'store'])->middleware('role:admin,teacher');
    Route::get('/{id}', [QuizController::class, 'show']);
    Route::put('/{id}', [QuizController::class, 'update'])->middleware('role:teacher');
    Route::delete('/{id}', [QuizController::class, 'destroy'])->middleware('role:admin,teacher');

    Route::post('/{quizId}/questions', [QuizController::class, 'addQuestion'])->middleware('role:teacher');
    Route::put('/questions/{id}', [QuizController::class, 'updateQuestion'])->middleware('role:teacher');
    Route::delete('/questions/{id}', [QuizController::class, 'deleteQuestion'])->middleware('role:teacher');

    Route::post('/{quizId}/start', [QuizController::class, 'start'])->middleware('role:student');
    Route::post('/attempts/{attemptId}/submit', [QuizController::class, 'submit'])->middleware('role:student');
    Route::get('/attempts', [QuizController::class, 'attemptsList']);
    Route::post('/attempts/{attemptId}/grade/{questionId}', [QuizController::class, 'gradeEssay'])->middleware('role:teacher');
});
