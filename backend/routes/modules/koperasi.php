<?php

use App\Modules\Koperasi\Controllers\KoperasiWebController;
use Illuminate\Support\Facades\Route;

// All Koperasi routes require auth + manage-pengaturan permission
Route::prefix('koperasi')->name('koperasi.')->middleware(['auth', 'role:permission:manage-pengaturan'])->group(function () {
    // Dashboard
    Route::get('/', [KoperasiWebController::class, 'index'])->name('index');

    // Products
    Route::get('/products', [KoperasiWebController::class, 'productsIndex'])->name('products');
    Route::get('/products/create', [KoperasiWebController::class, 'productsCreate'])->name('products.create');
    Route::post('/products', [KoperasiWebController::class, 'productsStore'])->name('products.store');
    Route::get('/products/{product}/edit', [KoperasiWebController::class, 'productsEdit'])->name('products.edit');
    Route::put('/products/{product}', [KoperasiWebController::class, 'productsUpdate'])->name('products.update');
    Route::delete('/products/{product}', [KoperasiWebController::class, 'productsDestroy'])->name('products.destroy');

    // Sales (POS)
    Route::get('/sales', [KoperasiWebController::class, 'salesIndex'])->name('sales');
    Route::post('/sales', [KoperasiWebController::class, 'salesStore'])->name('sales.store');
    Route::get('/sales/history', [KoperasiWebController::class, 'salesHistory'])->name('sales.history');

    // Savings
    Route::get('/savings', [KoperasiWebController::class, 'savingsIndex'])->name('savings');
    Route::post('/savings', [KoperasiWebController::class, 'savingsCreate'])->name('savings.create');
    Route::post('/savings/{saving}/transaction', [KoperasiWebController::class, 'savingsTransaction'])->name('savings.transaction');
    Route::get('/savings/{saving}/transactions', [KoperasiWebController::class, 'savingsTransactions'])->name('savings.transactions');
});
