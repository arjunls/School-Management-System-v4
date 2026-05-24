<?php

use App\Http\Middleware\LocaleMiddleware;
use App\Modules\Finance\Fee\Controllers\PembayaranController;
use App\Modules\Reporting\Export\Controllers\ExportController;
use App\Modules\Reporting\Import\Controllers\ImportController;
use App\Modules\StudentManagement\Attendance\Controllers\QRCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Login route (for auth middleware redirect)
Route::get('/login', function () {
    return view('welcome');
})->name('login');

// Language Switcher
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        LocaleMiddleware::setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Admin Panel Routes
Route::middleware(['auth', 'role:super-admin,admin,guru,wali-kelas,siswa,orang-tua,tata-usaha'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard')->middleware('role:permission:view-dashboard');

    // Siswa
    Route::get('/siswa', function () {
        return view('siswa.index');
    })->name('siswa.index')->middleware('role:permission:view-siswa');

    // Guru
    Route::get('/guru', function () {
        return view('guru.index');
    })->name('guru.index')->middleware('role:permission:view-guru');

    // Kelas
    Route::get('/kelas', function () {
        return view('kelas.index');
    })->name('kelas.index')->middleware('role:permission:view-kelas');

    // Kehadiran
    Route::get('/kehadiran', function () {
        return view('kehadiran.index');
    })->name('kehadiran.index')->middleware('role:permission:view-kehadiran');

    // QR Code Absensi
    Route::get('/qr/scanner', [QRCodeController::class, 'scanner'])->name('qr.scanner')->middleware('role:permission:edit-kehadiran');
    Route::get('/qr/{student}', [QRCodeController::class, 'show'])->name('qr.show')->middleware('role:permission:view-siswa');

    // QR Scan process (no CSRF for external scan)
    Route::get('/qr/scan/{studentId}/{token}', [QRCodeController::class, 'processScan'])->name('qr.scan.process')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    // Jadwal
    Route::get('/jadwal', function () {
        return view('jadwal.index');
    })->name('jadwal.index')->middleware('role:permission:view-jadwal');

    // Nilai
    Route::get('/nilai', function () {
        return view('nilai.index');
    })->name('nilai.index')->middleware('role:permission:view-nilai');

    // Pembayaran
    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/', [PembayaranController::class, 'index'])->name('index')->middleware('role:permission:view-pembayaran');
        Route::get('/create', [PembayaranController::class, 'create'])->name('create')->middleware('role:permission:create-pembayaran');
        Route::post('/', [PembayaranController::class, 'store'])->name('store')->middleware('role:permission:create-pembayaran');
        Route::post('/{invoice}/pay', [PembayaranController::class, 'pay'])->name('pay')->middleware('role:permission:edit-pembayaran');
        Route::get('/{invoice}/pay-online', [PembayaranController::class, 'payOnline'])->name('pay-online')->middleware('role:permission:edit-pembayaran');
        Route::delete('/{invoice}', [PembayaranController::class, 'destroy'])->name('destroy')->middleware('role:permission:delete-pembayaran');
    });

    // Midtrans notification (no CSRF)
    Route::post('/pembayaran/notification', [PembayaranController::class, 'notification'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    // Laporan
    Route::get('/laporan', function () {
        return view('laporan.index');
    })->name('laporan.index')->middleware('role:permission:view-laporan');

    // Dokumen
    Route::get('/dokumen', function () {
        return view('dokumen.index');
    })->name('dokumen.index')->middleware('role:permission:view-dokumen');

    // Profile
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');

    // Export
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/siswa', [ExportController::class, 'students'])->name('siswa')->middleware('role:permission:view-siswa');
        Route::get('/guru', [ExportController::class, 'teachers'])->name('guru')->middleware('role:permission:view-guru');
        Route::get('/nilai', [ExportController::class, 'grades'])->name('nilai')->middleware('role:permission:view-nilai');
        Route::get('/kehadiran', [ExportController::class, 'attendance'])->name('kehadiran')->middleware('role:permission:view-kehadiran');
    });

    // Import
    Route::prefix('import')->name('import.')->group(function () {
        Route::post('/siswa', [ImportController::class, 'students'])->name('siswa')->middleware('role:permission:create-siswa');
        Route::post('/nilai', [ImportController::class, 'grades'])->name('nilai')->middleware('role:permission:create-nilai');
        Route::post('/kehadiran', [ImportController::class, 'attendance'])->name('kehadiran')->middleware('role:permission:create-kehadiran');
    });
});
