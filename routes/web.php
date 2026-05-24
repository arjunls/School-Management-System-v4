<?php

use App\Http\Middleware\LocaleMiddleware;
use App\Modules\Auth\Controllers\LoginWebController;
use App\Modules\Finance\Fee\Controllers\PembayaranController;
use App\Modules\Reporting\Export\Controllers\ExportController;
use App\Modules\Reporting\Import\Controllers\ImportController;
use App\Modules\StudentManagement\Attendance\Controllers\QRCodeController;
use Illuminate\Support\Facades\Route;

// Web Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginWebController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginWebController::class, 'login'])->name('login.authenticate');
});

Route::post('/logout', [LoginWebController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/', function () {
    if (auth()->check()) return redirect('/dashboard');
    return redirect('/login');
});

// Language Switcher
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        LocaleMiddleware::setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Admin Panel Routes
Route::middleware(['auth', 'role:super-admin,admin,guru,wali-kelas,siswa,orang-tua,tata-usaha'])->group(function () {
    Route::get('/dashboard', [\App\Modules\Dashboard\Controllers\DashboardWebController::class, 'index'])->name('dashboard')->middleware('role:permission:view-dashboard');

    // Siswa
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'index'])->name('index')->middleware('role:permission:view-siswa');
        Route::get('/create', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'create'])->name('create')->middleware('role:permission:create-siswa');
        Route::post('/', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'store'])->name('store')->middleware('role:permission:create-siswa');
        Route::get('/{siswa}/edit', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'edit'])->name('edit')->middleware('role:permission:edit-siswa');
        Route::put('/{siswa}', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'update'])->name('update')->middleware('role:permission:edit-siswa');
        Route::delete('/{siswa}', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'destroy'])->name('destroy')->middleware('role:permission:delete-siswa');
    });

    // Guru
    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('/', [\App\Modules\StaffManagement\Teacher\Controllers\GuruWebController::class, 'index'])->name('index')->middleware('role:permission:view-guru');
        Route::get('/create', [\App\Modules\StaffManagement\Teacher\Controllers\GuruWebController::class, 'create'])->name('create')->middleware('role:permission:create-guru');
        Route::post('/', [\App\Modules\StaffManagement\Teacher\Controllers\GuruWebController::class, 'store'])->name('store')->middleware('role:permission:create-guru');
        Route::get('/{guru}/edit', [\App\Modules\StaffManagement\Teacher\Controllers\GuruWebController::class, 'edit'])->name('edit')->middleware('role:permission:edit-guru');
        Route::put('/{guru}', [\App\Modules\StaffManagement\Teacher\Controllers\GuruWebController::class, 'update'])->name('update')->middleware('role:permission:edit-guru');
        Route::delete('/{guru}', [\App\Modules\StaffManagement\Teacher\Controllers\GuruWebController::class, 'destroy'])->name('destroy')->middleware('role:permission:delete-guru');
    });

    // Kelas
    Route::prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/', [\App\Modules\Academic\Class\Controllers\KelasWebController::class, 'index'])->name('index')->middleware('role:permission:view-kelas');
        Route::get('/create', [\App\Modules\Academic\Class\Controllers\KelasWebController::class, 'create'])->name('create')->middleware('role:permission:create-kelas');
        Route::post('/', [\App\Modules\Academic\Class\Controllers\KelasWebController::class, 'store'])->name('store')->middleware('role:permission:create-kelas');
        Route::get('/{kelas}', [\App\Modules\Academic\Class\Controllers\KelasWebController::class, 'show'])->name('show')->middleware('role:permission:view-kelas');
        Route::get('/{kelas}/edit', [\App\Modules\Academic\Class\Controllers\KelasWebController::class, 'edit'])->name('edit')->middleware('role:permission:edit-kelas');
        Route::put('/{kelas}', [\App\Modules\Academic\Class\Controllers\KelasWebController::class, 'update'])->name('update')->middleware('role:permission:edit-kelas');
        Route::delete('/{kelas}', [\App\Modules\Academic\Class\Controllers\KelasWebController::class, 'destroy'])->name('destroy')->middleware('role:permission:delete-kelas');
    });

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
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/', [\App\Modules\Academic\Schedule\Controllers\JadwalWebController::class, 'index'])->name('index')->middleware('role:permission:view-jadwal');
        Route::get('/create', [\App\Modules\Academic\Schedule\Controllers\JadwalWebController::class, 'create'])->name('create')->middleware('role:permission:create-jadwal');
        Route::post('/', [\App\Modules\Academic\Schedule\Controllers\JadwalWebController::class, 'store'])->name('store')->middleware('role:permission:create-jadwal');
        Route::get('/{jadwal}/edit', [\App\Modules\Academic\Schedule\Controllers\JadwalWebController::class, 'edit'])->name('edit')->middleware('role:permission:edit-jadwal');
        Route::put('/{jadwal}', [\App\Modules\Academic\Schedule\Controllers\JadwalWebController::class, 'update'])->name('update')->middleware('role:permission:edit-jadwal');
        Route::delete('/{jadwal}', [\App\Modules\Academic\Schedule\Controllers\JadwalWebController::class, 'destroy'])->name('destroy')->middleware('role:permission:delete-jadwal');
    });

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
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [\App\Modules\Reporting\Report\Controllers\LaporanWebController::class, 'index'])->name('index')->middleware('role:permission:view-laporan');
        Route::get('/kehadiran', [\App\Modules\Reporting\Report\Controllers\LaporanWebController::class, 'attendance'])->name('attendance')->middleware('role:permission:view-laporan');
        Route::get('/nilai', [\App\Modules\Reporting\Report\Controllers\LaporanWebController::class, 'grades'])->name('grades')->middleware('role:permission:view-laporan');
        Route::get('/pembayaran', [\App\Modules\Reporting\Report\Controllers\LaporanWebController::class, 'payments'])->name('payments')->middleware('role:permission:view-laporan');
    });

    // Activity Log
    Route::get('/aktivitas', [\App\Modules\Activity\Controllers\ActivityLogWebController::class, 'index'])->name('activity.index')->middleware('role:permission:view-laporan');

    // Dokumen
    Route::prefix('dokumen')->name('dokumen.')->group(function () {
        Route::get('/', [\App\Modules\Upload\Controllers\DokumenWebController::class, 'index'])->name('index')->middleware('role:permission:view-dokumen');
        Route::post('/', [\App\Modules\Upload\Controllers\DokumenWebController::class, 'store'])->name('store')->middleware('role:permission:create-dokumen');
        Route::get('/{dokumen}/download', [\App\Modules\Upload\Controllers\DokumenWebController::class, 'download'])->name('download')->middleware('role:permission:view-dokumen');
        Route::delete('/{dokumen}', [\App\Modules\Upload\Controllers\DokumenWebController::class, 'destroy'])->name('destroy')->middleware('role:permission:delete-dokumen');
    });

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
