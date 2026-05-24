<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Calendar\Controllers\EventController;

// Calendar / Event routes
Route::prefix('events')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/', [EventController::class, 'store'])->middleware('role:admin,teacher');
    Route::put('/{id}', [EventController::class, 'update'])->middleware('role:admin,teacher');
    Route::delete('/{id}', [EventController::class, 'destroy'])->middleware('role:admin');
});
