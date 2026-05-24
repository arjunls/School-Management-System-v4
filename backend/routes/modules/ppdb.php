<?php

use App\Modules\Ppdb\Controllers\PpdbWebController;
use Illuminate\Support\Facades\Route;

// Public routes (no auth)
Route::prefix('ppdb')->name('ppdb.')->group(function () {
    Route::get('/', [PpdbWebController::class, 'index'])->name('index');
    Route::post('/register', [PpdbWebController::class, 'register'])->name('register');
});

// Admin routes (auth + manage-pengaturan permission)
Route::prefix('ppdb/admin')->name('ppdb.admin.')->middleware(['auth', 'role:permission:manage-pengaturan'])->group(function () {
    Route::get('/', [PpdbWebController::class, 'adminIndex'])->name('index');

    // Periods
    Route::get('/periods', [PpdbWebController::class, 'adminPeriods'])->name('periods');
    Route::get('/periods/create', [PpdbWebController::class, 'adminPeriodsCreate'])->name('periods.create');
    Route::post('/periods', [PpdbWebController::class, 'adminPeriodsStore'])->name('periods.store');
    Route::get('/periods/{period}/edit', [PpdbWebController::class, 'adminPeriodsEdit'])->name('periods.edit');
    Route::put('/periods/{period}', [PpdbWebController::class, 'adminPeriodsUpdate'])->name('periods.update');
    Route::delete('/periods/{period}', [PpdbWebController::class, 'adminPeriodsDestroy'])->name('periods.destroy');

    // Applicants
    Route::get('/applicants', [PpdbWebController::class, 'adminApplicants'])->name('applicants');
    Route::get('/applicants/{applicant}', [PpdbWebController::class, 'adminApplicantsShow'])->name('applicants.show');
    Route::put('/applicants/{applicant}/status', [PpdbWebController::class, 'adminApplicantsUpdateStatus'])->name('applicants.update-status');
});
