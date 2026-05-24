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

// PWA Manifest
Route::get('/manifest.json', function () {
    return response()->json([
        'name' => 'SMK Management V4',
        'short_name' => 'SMK V4',
        'start_url' => '/',
        'display' => 'standalone',
        'background_color' => '#f8fafc',
        'theme_color' => '#1e293b',
        'icons' => [
            [
                'src' => '/icons/192',
                'sizes' => '192x192',
                'type' => 'image/svg+xml',
            ],
            [
                'src' => '/icons/512',
                'sizes' => '512x512',
                'type' => 'image/svg+xml',
            ],
        ],
    ]);
})->name('manifest');

// PWA Service Worker
Route::get('/serviceworker.js', function () {
    $content = <<<'JS'
const CACHE_NAME = 'smk-v4-cache-v1';
const urlsToCache = ['/', '/login'];

self.addEventListener('install', event => {
    event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache)));
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => response || fetch(event.request))
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(caches.keys().then(names => Promise.all(names.map(n => {
        if (n !== CACHE_NAME) return caches.delete(n);
    }))));
});
JS;
    return response($content)->header('Content-Type', 'application/javascript');
})->name('serviceworker');

// PWA Icons (inline SVG placeholder)
Route::get('/icons/{size}', function (string $size) {
    $dim = (int) $size;
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$dim.'" height="'.$dim.'" viewBox="0 0 '.$dim.' '.$dim.'"><rect width="'.$dim.'" height="'.$dim.'" fill="#1e293b"/><text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" font-family="sans-serif" font-size="'.($dim*0.4).'" font-weight="bold" fill="#ffffff">SMK</text></svg>';
    return response($svg)->header('Content-Type', 'image/svg+xml');
})->where('size', '\d+')->name('pwa.icons');

// Admin Panel Routes
Route::middleware(['auth', 'role:super-admin,admin,guru,wali-kelas,siswa,orang-tua,tata-usaha'])->group(function () {
    Route::get('/dashboard', [\App\Modules\Dashboard\Controllers\DashboardWebController::class, 'index'])->name('dashboard')->middleware('role:permission:view-dashboard');

    // Siswa Portal (role: siswa)
    Route::prefix('siswa')->name('siswa.portal.')->middleware('role:siswa')->group(function () {
        Route::get('/dashboard', [\App\Modules\StudentManagement\Student\Controllers\SiswaPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/nilai', [\App\Modules\StudentManagement\Student\Controllers\SiswaPortalController::class, 'grades'])->name('grades');
        Route::get('/kehadiran', [\App\Modules\StudentManagement\Student\Controllers\SiswaPortalController::class, 'attendance'])->name('attendance');
        Route::get('/jadwal', [\App\Modules\StudentManagement\Student\Controllers\SiswaPortalController::class, 'schedule'])->name('schedule');
        Route::get('/tagihan', [\App\Modules\StudentManagement\Student\Controllers\SiswaPortalController::class, 'payments'])->name('payments');
    });

    // Orang Tua Portal (role: orang-tua)
    Route::prefix('orang-tua')->name('orangtua.portal.')->middleware('role:orang-tua')->group(function () {
        Route::get('/dashboard', [\App\Modules\StudentManagement\Parent\Controllers\OrangTuaPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/nilai/{studentId}', [\App\Modules\StudentManagement\Parent\Controllers\OrangTuaPortalController::class, 'grades'])->name('grades');
        Route::get('/kehadiran/{studentId}', [\App\Modules\StudentManagement\Parent\Controllers\OrangTuaPortalController::class, 'attendance'])->name('attendance');
        Route::get('/jadwal/{studentId}', [\App\Modules\StudentManagement\Parent\Controllers\OrangTuaPortalController::class, 'schedule'])->name('schedule');
        Route::get('/tagihan/{studentId}', [\App\Modules\StudentManagement\Parent\Controllers\OrangTuaPortalController::class, 'payments'])->name('payments');
    });

    // Siswa CRUD
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'index'])->name('index')->middleware('role:permission:view-siswa');
        Route::get('/create', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'create'])->name('create')->middleware('role:permission:create-siswa');
        Route::post('/', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'store'])->name('store')->middleware('role:permission:create-siswa');
        Route::get('/{siswa}/edit', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'edit'])->name('edit')->middleware('role:permission:edit-siswa');
        Route::put('/{siswa}', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'update'])->name('update')->middleware('role:permission:edit-siswa');
        Route::delete('/{siswa}', [\App\Modules\StudentManagement\Student\Controllers\SiswaWebController::class, 'destroy'])->name('destroy')->middleware('role:permission:delete-siswa');
    });

    // Guru Portal (role: guru, wali-kelas)
    Route::prefix('guru')->name('guru.portal.')->middleware('role:guru,wali-kelas')->group(function () {
        Route::get('/dashboard', [\App\Modules\StaffManagement\Teacher\Controllers\GuruPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/jadwal', [\App\Modules\StaffManagement\Teacher\Controllers\GuruPortalController::class, 'schedule'])->name('schedule');
        Route::get('/{kelas}/nilai', [\App\Modules\StaffManagement\Teacher\Controllers\GuruPortalController::class, 'grades'])->name('grades');
        Route::post('/{kelas}/nilai', [\App\Modules\StaffManagement\Teacher\Controllers\GuruPortalController::class, 'storeGrades'])->name('grades.store');
        Route::get('/{kelas}/absensi', [\App\Modules\StaffManagement\Teacher\Controllers\GuruPortalController::class, 'attendance'])->name('attendance');
        Route::post('/{kelas}/absensi', [\App\Modules\StaffManagement\Teacher\Controllers\GuruPortalController::class, 'storeAttendance'])->name('attendance.store');
    });

    // Guru CRUD
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

    // Mata Pelajaran
    Route::prefix('mapel')->name('mapel.')->group(function () {
        Route::get('/', [\App\Modules\Academic\Subject\Controllers\SubjectWebController::class, 'index'])->name('index')->middleware('role:permission:view-jadwal');
        Route::get('/create', [\App\Modules\Academic\Subject\Controllers\SubjectWebController::class, 'create'])->name('create')->middleware('role:permission:create-jadwal');
        Route::post('/', [\App\Modules\Academic\Subject\Controllers\SubjectWebController::class, 'store'])->name('store')->middleware('role:permission:create-jadwal');
        Route::get('/{mapel}/edit', [\App\Modules\Academic\Subject\Controllers\SubjectWebController::class, 'edit'])->name('edit')->middleware('role:permission:edit-jadwal');
        Route::put('/{mapel}', [\App\Modules\Academic\Subject\Controllers\SubjectWebController::class, 'update'])->name('update')->middleware('role:permission:edit-jadwal');
        Route::delete('/{mapel}', [\App\Modules\Academic\Subject\Controllers\SubjectWebController::class, 'destroy'])->name('destroy')->middleware('role:permission:delete-jadwal');
    });

    // Dokumen
    Route::prefix('dokumen')->name('dokumen.')->group(function () {
        Route::get('/', [\App\Modules\Upload\Controllers\DokumenWebController::class, 'index'])->name('index')->middleware('role:permission:view-dokumen');
        Route::post('/', [\App\Modules\Upload\Controllers\DokumenWebController::class, 'store'])->name('store')->middleware('role:permission:create-dokumen');
        Route::get('/{dokumen}/download', [\App\Modules\Upload\Controllers\DokumenWebController::class, 'download'])->name('download')->middleware('role:permission:view-dokumen');
        Route::delete('/{dokumen}', [\App\Modules\Upload\Controllers\DokumenWebController::class, 'destroy'])->name('destroy')->middleware('role:permission:delete-dokumen');
    });

    // Notifikasi
    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/', [\App\Modules\Communication\Notification\Controllers\NotificationWebController::class, 'index'])->name('index');
        Route::post('/{id}/read', [\App\Modules\Communication\Notification\Controllers\NotificationWebController::class, 'markAsRead'])->name('markRead');
        Route::post('/mark-all-read', [\App\Modules\Communication\Notification\Controllers\NotificationWebController::class, 'markAllAsRead'])->name('markAllRead');
    });

    // Rapor Digital
    Route::prefix('rapor')->name('rapor.')->middleware('role:permission:view-laporan')->group(function () {
        Route::get('/', [\App\Modules\Reporting\Report\Controllers\RaporWebController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Modules\Reporting\Report\Controllers\RaporWebController::class, 'generate'])->name('generate');
        Route::post('/preview', [\App\Modules\Reporting\Report\Controllers\RaporWebController::class, 'preview'])->name('preview');
    });

    // Profile
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');

    // Manajemen Pengguna
    Route::prefix('pengguna')->name('pengguna.')->middleware('role:permission:manage-users')->group(function () {
        Route::get('/', [\App\Modules\StaffManagement\User\Controllers\UserWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\StaffManagement\User\Controllers\UserWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\StaffManagement\User\Controllers\UserWebController::class, 'store'])->name('store');
        Route::get('/{pengguna}/edit', [\App\Modules\StaffManagement\User\Controllers\UserWebController::class, 'edit'])->name('edit');
        Route::put('/{pengguna}', [\App\Modules\StaffManagement\User\Controllers\UserWebController::class, 'update'])->name('update');
        Route::delete('/{pengguna}', [\App\Modules\StaffManagement\User\Controllers\UserWebController::class, 'destroy'])->name('destroy');
    });

    // Pengaturan
    Route::prefix('pengaturan')->name('pengaturan.')->middleware('role:permission:manage-pengaturan')->group(function () {
        Route::get('/', [\App\Modules\StaffManagement\User\Controllers\PengaturanController::class, 'index'])->name('index');
        Route::put('/', [\App\Modules\StaffManagement\User\Controllers\PengaturanController::class, 'update'])->name('update');
    });

    // Backup Database
    Route::prefix('backup')->name('backup.')->middleware('role:permission:manage-pengaturan')->group(function () {
        Route::get('/', [\App\Modules\Backup\Controllers\BackupController::class, 'index'])->name('index');
        Route::post('/create', [\App\Modules\Backup\Controllers\BackupController::class, 'create'])->name('create');
        Route::get('/{filename}/download', [\App\Modules\Backup\Controllers\BackupController::class, 'download'])->name('download');
        Route::delete('/{filename}', [\App\Modules\Backup\Controllers\BackupController::class, 'destroy'])->name('destroy');
    });

    // Export
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/siswa', [ExportController::class, 'students'])->name('siswa')->middleware('role:permission:view-siswa');
        Route::get('/guru', [ExportController::class, 'teachers'])->name('guru')->middleware('role:permission:view-guru');
        Route::get('/nilai', [ExportController::class, 'grades'])->name('nilai')->middleware('role:permission:view-nilai');
        Route::get('/kehadiran', [ExportController::class, 'attendance'])->name('kehadiran')->middleware('role:permission:view-kehadiran');
        Route::get('/kelas', [ExportController::class, 'kelas'])->name('kelas')->middleware('role:permission:view-kelas');
        Route::get('/jadwal', [ExportController::class, 'jadwal'])->name('jadwal')->middleware('role:permission:view-jadwal');
        Route::get('/pembayaran', [ExportController::class, 'pembayaran'])->name('pembayaran')->middleware('role:permission:view-pembayaran');
    });

    // Import
    Route::prefix('import')->name('import.')->group(function () {
        Route::post('/siswa', [ImportController::class, 'students'])->name('siswa')->middleware('role:permission:create-siswa');
        Route::post('/nilai', [ImportController::class, 'grades'])->name('nilai')->middleware('role:permission:create-nilai');
        Route::post('/kehadiran', [ImportController::class, 'attendance'])->name('kehadiran')->middleware('role:permission:create-kehadiran');
    });
});
