<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Library\Controllers\LibraryController;

// Library routes
Route::prefix('library')->middleware('auth:sanctum')->group(function () {
    Route::get('/books', [LibraryController::class, 'books']);
    Route::post('/books', [LibraryController::class, 'bookStore'])->middleware('role:admin,teacher');
    Route::put('/books/{id}', [LibraryController::class, 'bookUpdate'])->middleware('role:admin,teacher');
    Route::delete('/books/{id}', [LibraryController::class, 'bookDelete'])->middleware('role:admin,teacher');
    Route::get('/loans', [LibraryController::class, 'loans']);
    Route::post('/loans', [LibraryController::class, 'loanStore'])->middleware('role:admin,teacher');
    Route::post('/loans/{id}/return', [LibraryController::class, 'loanReturn'])->middleware('role:admin,teacher');
});
