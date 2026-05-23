<?php

use Illuminate\Support\Facades\Route;

// Audit Log routes (admin only, from Kernel)
Route::prefix('audit-logs')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [\App\Kernel\Audit\Controllers\AuditLogController::class, 'index']);
    Route::get('/{id}', [\App\Kernel\Audit\Controllers\AuditLogController::class, 'show']);
});
