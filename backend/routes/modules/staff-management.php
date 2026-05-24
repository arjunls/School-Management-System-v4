<?php

use Illuminate\Support\Facades\Route;
use App\Modules\StaffManagement\Teacher\Controllers\TeacherController;
use App\Modules\StaffManagement\User\Controllers\UserController;

// Teacher Routes
Route::prefix('teachers')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TeacherController::class, 'getAllTeachers']);
    Route::get('/paginated', [TeacherController::class, 'getTeachersPaginated']);
    Route::get('/{id}', [TeacherController::class, 'getTeacher']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/', [TeacherController::class, 'createTeacher']);
        Route::put('/{id}', [TeacherController::class, 'updateTeacher']);
        Route::delete('/{id}', [TeacherController::class, 'deleteTeacher']);
    });
});

// User Management (admin only)
Route::prefix('users')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [UserController::class, 'getAllUsers']);
    Route::get('/paginated', [UserController::class, 'getUsersPaginated']);
    Route::get('/{id}', [UserController::class, 'getUser']);
    Route::post('/', [UserController::class, 'createUser']);
    Route::put('/{id}', [UserController::class, 'updateUser']);
    Route::delete('/{id}', [UserController::class, 'deleteUser']);
    Route::get('/by-email', [UserController::class, 'getUserByEmail']);
});
