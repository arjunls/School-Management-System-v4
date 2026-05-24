<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Finance\Fee\Controllers\FeeController;

// Fee routes
Route::prefix('fees')->middleware('auth:sanctum')->group(function () {
    Route::get('/types', [FeeController::class, 'types']);
    Route::post('/types', [FeeController::class, 'typeStore'])->middleware('role:admin');
    Route::put('/types/{id}', [FeeController::class, 'typeUpdate'])->middleware('role:admin');
    Route::delete('/types/{id}', [FeeController::class, 'typeDelete'])->middleware('role:admin');
    Route::get('/invoices', [FeeController::class, 'invoices']);
    Route::post('/invoices', [FeeController::class, 'invoiceStore'])->middleware('role:admin');
    Route::post('/invoices/{invoiceId}/pay', [FeeController::class, 'pay'])->middleware('role:admin,student');
});
