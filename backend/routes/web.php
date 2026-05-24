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

// Theme Toggle (must be before auth group for fast response)
Route::post('/theme/toggle', function (\Illuminate\Http\Request $r) {
    \App\Models\Setting::setValue('dark_mode_' . auth()->id(), $r->input('dark_mode', 'false'));
    return response()->json(['success' => true]);
})->name('theme.toggle')->middleware('auth');

Route::get('/', function () {
    if (auth()->check()) return redirect('/dashboard');
    return redirect('/login');
});

// Website Publik (CMS) - Public Routes
Route::prefix('blog')->name('website.')->group(function () {
    Route::get('/', [\App\Modules\Website\Controllers\WebsiteWebController::class, 'index'])->name('index');
    Route::get('/{post}', [\App\Modules\Website\Controllers\WebsiteWebController::class, 'show'])->name('show');
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

    // Pesan Internal
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [\App\Modules\Communication\Message\Controllers\MessageWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Communication\Message\Controllers\MessageWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Communication\Message\Controllers\MessageWebController::class, 'store'])->name('store');
        Route::get('/{message}', [\App\Modules\Communication\Message\Controllers\MessageWebController::class, 'show'])->name('show');
        Route::post('/{message}/reply', [\App\Modules\Communication\Message\Controllers\MessageWebController::class, 'reply'])->name('reply');
    });

    // Pengumuman
    Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
        Route::get('/', [\App\Modules\Communication\Announcement\Controllers\AnnouncementWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Communication\Announcement\Controllers\AnnouncementWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Communication\Announcement\Controllers\AnnouncementWebController::class, 'store'])->name('store');
        Route::delete('/{pengumuman}', [\App\Modules\Communication\Announcement\Controllers\AnnouncementWebController::class, 'destroy'])->name('destroy');
    });

    // Rapor Digital
    Route::prefix('rapor')->name('rapor.')->middleware('role:permission:view-laporan')->group(function () {
        Route::get('/', [\App\Modules\Reporting\Report\Controllers\RaporWebController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Modules\Reporting\Report\Controllers\RaporWebController::class, 'generate'])->name('generate');
        Route::post('/preview', [\App\Modules\Reporting\Report\Controllers\RaporWebController::class, 'preview'])->name('preview');
        Route::post('/batch', [\App\Modules\Reporting\Report\Controllers\RaporWebController::class, 'generateBatch'])->name('batch');
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

    // Kalender Akademik
    Route::prefix('kalender')->name('kalender.')->group(function () {
        Route::get('/', [\App\Modules\Calendar\Controllers\CalendarWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Calendar\Controllers\CalendarWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Calendar\Controllers\CalendarWebController::class, 'store'])->name('store');
        Route::delete('/{kalender}', [\App\Modules\Calendar\Controllers\CalendarWebController::class, 'destroy'])->name('destroy');
    });

    // Perpustakaan
    Route::prefix('perpustakaan')->name('perpustakaan.')->group(function () {
        Route::get('/', [\App\Modules\Library\Controllers\LibraryWebController::class, 'index'])->name('index');
        Route::post('/', [\App\Modules\Library\Controllers\LibraryWebController::class, 'store'])->name('store');
        Route::post('/borrow', [\App\Modules\Library\Controllers\LibraryWebController::class, 'borrow'])->name('borrow');
        Route::post('/{pinjaman}/return', [\App\Modules\Library\Controllers\LibraryWebController::class, 'returnBook'])->name('return');
        Route::delete('/{buku}', [\App\Modules\Library\Controllers\LibraryWebController::class, 'destroy'])->name('destroy');
    });

    // UKS / Klinik
    Route::prefix('uks')->name('uks.')->group(function () {
        Route::get('/', [\App\Modules\StudentManagement\Health\Controllers\HealthWebController::class, 'index'])->name('index');
        Route::post('/', [\App\Modules\StudentManagement\Health\Controllers\HealthWebController::class, 'store'])->name('store');
        Route::delete('/{uk}', [\App\Modules\StudentManagement\Health\Controllers\HealthWebController::class, 'destroy'])->name('destroy');
    });

    // Ekstrakurikuler
    Route::prefix('ekskul')->name('ekskul.')->group(function () {
        Route::get('/', [\App\Modules\StudentLife\Extracurricular\Controllers\ExtracurricularWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\StudentLife\Extracurricular\Controllers\ExtracurricularWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\StudentLife\Extracurricular\Controllers\ExtracurricularWebController::class, 'store'])->name('store');
        Route::get('/{ekskul}/edit', [\App\Modules\StudentLife\Extracurricular\Controllers\ExtracurricularWebController::class, 'edit'])->name('edit');
        Route::put('/{ekskul}', [\App\Modules\StudentLife\Extracurricular\Controllers\ExtracurricularWebController::class, 'update'])->name('update');
        Route::delete('/{ekskul}', [\App\Modules\StudentLife\Extracurricular\Controllers\ExtracurricularWebController::class, 'destroy'])->name('destroy');
    });

    // BK (Bimbingan Konseling)
    Route::prefix('bk')->name('bk.')->group(function () {
        Route::get('/', [\App\Modules\BkControllers\BkWebController::class, 'index'])->name('index');
        Route::post('/', [\App\Modules\BkControllers\BkWebController::class, 'store'])->name('store');
        Route::delete('/{bk}', [\App\Modules\BkControllers\BkWebController::class, 'destroy'])->name('destroy');
    });

    // Pelanggaran
    Route::prefix('pelanggaran')->name('pelanggaran.')->group(function () {
        Route::get('/', [\App\Modules\PelanggaranControllers\PelanggaranWebController::class, 'index'])->name('index');
        Route::post('/violation', [\App\Modules\PelanggaranControllers\PelanggaranWebController::class, 'storeViolation'])->name('violation.store');
        Route::post('/record', [\App\Modules\PelanggaranControllers\PelanggaranWebController::class, 'storeRecord'])->name('record.store');
        Route::delete('/violation/{pelanggaran}', [\App\Modules\PelanggaranControllers\PelanggaranWebController::class, 'destroyViolation'])->name('violation.destroy');
        Route::delete('/record/{catatan}', [\App\Modules\PelanggaranControllers\PelanggaranWebController::class, 'destroyRecord'])->name('record.destroy');
    });

    // Prestasi
    Route::prefix('prestasi')->name('prestasi.')->group(function () {
        Route::get('/', [\App\Modules\PrestasiControllers\PrestasiWebController::class, 'index'])->name('index');
        Route::post('/', [\App\Modules\PrestasiControllers\PrestasiWebController::class, 'store'])->name('store');
        Route::delete('/{prestasi}', [\App\Modules\PrestasiControllers\PrestasiWebController::class, 'destroy'])->name('destroy');
    });

    // Role & Permission Manager
    Route::prefix('roles')->name('roles.')->middleware('role:permission:manage-users')->group(function () {
        Route::get('/', [\App\Modules\RolePermission\Controllers\RoleWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\RolePermission\Controllers\RoleWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\RolePermission\Controllers\RoleWebController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [\App\Modules\RolePermission\Controllers\RoleWebController::class, 'edit'])->name('edit');
        Route::put('/{role}', [\App\Modules\RolePermission\Controllers\RoleWebController::class, 'update'])->name('update');
        Route::delete('/{role}', [\App\Modules\RolePermission\Controllers\RoleWebController::class, 'destroy'])->name('destroy');
    });

    // Tugas / LKPD Digital
    Route::prefix('tugas')->name('tugas.')->group(function () {
        Route::get('/', [\App\Modules\Learning\Assignment\Controllers\AssignmentWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Learning\Assignment\Controllers\AssignmentWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Learning\Assignment\Controllers\AssignmentWebController::class, 'store'])->name('store');
        Route::get('/{tugas}/edit', [\App\Modules\Learning\Assignment\Controllers\AssignmentWebController::class, 'edit'])->name('edit');
        Route::put('/{tugas}', [\App\Modules\Learning\Assignment\Controllers\AssignmentWebController::class, 'update'])->name('update');
        Route::delete('/{tugas}', [\App\Modules\Learning\Assignment\Controllers\AssignmentWebController::class, 'destroy'])->name('destroy');
        Route::get('/{tugas}/submissions', [\App\Modules\Learning\Assignment\Controllers\AssignmentWebController::class, 'submissions'])->name('submissions');
        Route::post('/submissions/{pengumpulan}/grade', [\App\Modules\Learning\Assignment\Controllers\AssignmentWebController::class, 'grade'])->name('grade');
    });

    // Jadwal Ujian
    Route::prefix('jadwal-ujian')->name('jadwal-ujian.')->group(function () {
        Route::get('/', [\App\Modules\Learning\ExamSchedule\Controllers\ExamScheduleWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Learning\ExamSchedule\Controllers\ExamScheduleWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Learning\ExamSchedule\Controllers\ExamScheduleWebController::class, 'store'])->name('store');
        Route::get('/{ujian}/edit', [\App\Modules\Learning\ExamSchedule\Controllers\ExamScheduleWebController::class, 'edit'])->name('edit');
        Route::put('/{ujian}', [\App\Modules\Learning\ExamSchedule\Controllers\ExamScheduleWebController::class, 'update'])->name('update');
        Route::delete('/{ujian}', [\App\Modules\Learning\ExamSchedule\Controllers\ExamScheduleWebController::class, 'destroy'])->name('destroy');
    });

    // Kuis Online / CBT
    Route::prefix('kuis')->name('kuis.')->group(function () {
        Route::get('/', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'store'])->name('store');
        Route::get('/{kuis}/edit', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'edit'])->name('edit');
        Route::put('/{kuis}', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'update'])->name('update');
        Route::delete('/{kuis}', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'destroy'])->name('destroy');
        Route::get('/{kuis}/questions', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'questions'])->name('questions');
        Route::post('/{kuis}/questions', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'storeQuestion'])->name('question.store');
        Route::delete('/questions/{soal}', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'destroyQuestion'])->name('question.destroy');
        // CBT Student
        Route::get('/{kuis}/take', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'take'])->name('take');
        Route::post('/attempt/{attempt}/submit', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'submit'])->name('submit');
        Route::get('/attempt/{attempt}/result', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'result'])->name('result');
        // CBT Teacher Grading
        Route::get('/{kuis}/grades', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'grades'])->name('grades');
        Route::get('/attempt/{attempt}/grade', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'gradeAttempt'])->name('gradeAttempt');
        Route::post('/attempt/{attempt}/grade/{question}', [\App\Modules\Learning\Quiz\Controllers\QuizWebController::class, 'gradeQuestion'])->name('gradeQuestion');
    });

    // Projek P5
    Route::prefix('p5')->name('p5.')->group(function () {
        Route::get('/', [\App\Modules\StudentLife\P5\Controllers\P5WebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\StudentLife\P5\Controllers\P5WebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\StudentLife\P5\Controllers\P5WebController::class, 'store'])->name('store');
        Route::get('/{p5}', [\App\Modules\StudentLife\P5\Controllers\P5WebController::class, 'show'])->name('show');
        Route::get('/{p5}/edit', [\App\Modules\StudentLife\P5\Controllers\P5WebController::class, 'edit'])->name('edit');
        Route::put('/{p5}', [\App\Modules\StudentLife\P5\Controllers\P5WebController::class, 'update'])->name('update');
        Route::delete('/{p5}', [\App\Modules\StudentLife\P5\Controllers\P5WebController::class, 'destroy'])->name('destroy');
        Route::post('/{p5}/activities', [\App\Modules\StudentLife\P5\Controllers\P5WebController::class, 'storeActivity'])->name('activity.store');
        Route::delete('/activities/{kegiatan}', [\App\Modules\StudentLife\P5\Controllers\P5WebController::class, 'destroyActivity'])->name('activity.destroy');
    });

    // PKL / Prakerin
    Route::prefix('pkl')->name('pkl.')->group(function () {
        Route::get('/', [\App\Modules\StudentLife\PKL\Controllers\PKLWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\StudentLife\PKL\Controllers\PKLWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\StudentLife\PKL\Controllers\PKLWebController::class, 'store'])->name('store');
        Route::get('/{pkl}/edit', [\App\Modules\StudentLife\PKL\Controllers\PKLWebController::class, 'edit'])->name('edit');
        Route::put('/{pkl}', [\App\Modules\StudentLife\PKL\Controllers\PKLWebController::class, 'update'])->name('update');
        Route::delete('/{pkl}', [\App\Modules\StudentLife\PKL\Controllers\PKLWebController::class, 'destroy'])->name('destroy');
    });

    // Alumni
    Route::prefix('alumni')->name('alumni.')->group(function () {
        Route::get('/', [\App\Modules\StudentLife\Alumni\Controllers\AlumniWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\StudentLife\Alumni\Controllers\AlumniWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\StudentLife\Alumni\Controllers\AlumniWebController::class, 'store'])->name('store');
        Route::get('/{alumni}/edit', [\App\Modules\StudentLife\Alumni\Controllers\AlumniWebController::class, 'edit'])->name('edit');
        Route::put('/{alumni}', [\App\Modules\StudentLife\Alumni\Controllers\AlumniWebController::class, 'update'])->name('update');
        Route::delete('/{alumni}', [\App\Modules\StudentLife\Alumni\Controllers\AlumniWebController::class, 'destroy'])->name('destroy');
    });

    // Jurnal Mengajar Guru
    Route::prefix('jurnal')->name('jurnal.')->group(function () {
        Route::get('/', [\App\Modules\Learning\TeachingLog\Controllers\JurnalWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Learning\TeachingLog\Controllers\JurnalWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Learning\TeachingLog\Controllers\JurnalWebController::class, 'store'])->name('store');
        Route::get('/{jurnal}/edit', [\App\Modules\Learning\TeachingLog\Controllers\JurnalWebController::class, 'edit'])->name('edit');
        Route::put('/{jurnal}', [\App\Modules\Learning\TeachingLog\Controllers\JurnalWebController::class, 'update'])->name('update');
        Route::delete('/{jurnal}', [\App\Modules\Learning\TeachingLog\Controllers\JurnalWebController::class, 'destroy'])->name('destroy');
    });

    // Import
    Route::prefix('polling')->name('polling.')->group(function () {
        Route::get('/', [\App\Modules\PollingControllers\PollingWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\PollingControllers\PollingWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\PollingControllers\PollingWebController::class, 'store'])->name('store');
        Route::post('/{polling}/vote', [\App\Modules\PollingControllers\PollingWebController::class, 'vote'])->name('vote');
        Route::delete('/{polling}', [\App\Modules\PollingControllers\PollingWebController::class, 'destroy'])->name('destroy');
    });

    // Import
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('/', function () { return view('import.index'); })->name('index');
        Route::post('/siswa', [ImportController::class, 'students'])->name('siswa')->middleware('role:permission:create-siswa');
        Route::post('/nilai', [ImportController::class, 'grades'])->name('nilai')->middleware('role:permission:create-nilai');
        Route::post('/kehadiran', [ImportController::class, 'attendance'])->name('kehadiran')->middleware('role:permission:create-kehadiran');
        Route::post('/kelas', [ImportController::class, 'classes'])->name('kelas')->middleware('role:permission:create-kelas');
        Route::post('/mapel', [ImportController::class, 'subjects'])->name('mapel')->middleware('role:permission:create-jadwal');
        Route::post('/jadwal', [ImportController::class, 'schedules'])->name('jadwal')->middleware('role:permission:create-jadwal');
        Route::post('/guru', [ImportController::class, 'teachers'])->name('guru')->middleware('role:permission:create-guru');
        Route::post('/pembayaran', [ImportController::class, 'payments'])->name('pembayaran')->middleware('role:permission:create-pembayaran');
    });

    // Website Admin (CMS)
    Route::prefix('website')->name('website.admin.')->middleware('role:permission:manage-pengaturan')->group(function () {
        Route::get('/', [\App\Modules\Website\Controllers\WebsiteWebController::class, 'adminIndex'])->name('index');
        Route::get('/create', [\App\Modules\Website\Controllers\WebsiteWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Website\Controllers\WebsiteWebController::class, 'store'])->name('store');
        Route::get('/{post}/edit', [\App\Modules\Website\Controllers\WebsiteWebController::class, 'edit'])->name('edit');
        Route::put('/{post}', [\App\Modules\Website\Controllers\WebsiteWebController::class, 'update'])->name('update');
        Route::delete('/{post}', [\App\Modules\Website\Controllers\WebsiteWebController::class, 'destroy'])->name('destroy');
    });

    // Pages Admin
    Route::prefix('pages')->name('website.pages.')->middleware('role:permission:manage-pengaturan')->group(function () {
        Route::get('/', [\App\Modules\Website\Controllers\PageWebController::class, 'index'])->name('index');
        Route::get('/{page}/edit', [\App\Modules\Website\Controllers\PageWebController::class, 'edit'])->name('edit');
        Route::put('/{page}', [\App\Modules\Website\Controllers\PageWebController::class, 'update'])->name('update');
    });

    // Galleries Admin
    Route::prefix('galleries')->name('website.galleries.')->middleware('role:permission:manage-pengaturan')->group(function () {
        Route::get('/', [\App\Modules\Website\Controllers\GalleryWebController::class, 'index'])->name('index');
        Route::post('/', [\App\Modules\Website\Controllers\GalleryWebController::class, 'store'])->name('store');
        Route::delete('/{gallery}', [\App\Modules\Website\Controllers\GalleryWebController::class, 'destroy'])->name('destroy');
    });

    // Kurikulum Merdeka
    Route::prefix('curriculum')->name('curriculum.')->group(function () {
        Route::get('/', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'store'])->name('store');
        Route::get('/{cp}', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'show'])->name('show');
        Route::get('/{cp}/edit', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'edit'])->name('edit');
        Route::put('/{cp}', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'update'])->name('update');
        Route::delete('/{cp}', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'destroy'])->name('destroy');

        // TP nested
        Route::post('/{cp}/tp', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'storeTp'])->name('tp.store');
        Route::put('/{cp}/tp/{tp}', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'updateTp'])->name('tp.update');
        Route::delete('/{cp}/tp/{tp}', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'destroyTp'])->name('tp.destroy');

        // ATP nested
        Route::post('/{cp}/tp/{tp}/atp', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'storeAtp'])->name('atp.store');
        Route::put('/{cp}/tp/{tp}/atp/{atp}', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'updateAtp'])->name('atp.update');
        Route::delete('/{cp}/tp/{tp}/atp/{atp}', [\App\Modules\Curriculum\Controllers\CurriculumWebController::class, 'destroyAtp'])->name('atp.destroy');
    });

    // Cetak Massal
    Route::prefix('cetak')->name('printing.')->group(function () {
        Route::get('/', [\App\Modules\Printing\Controllers\PrintingWebController::class, 'index'])->name('index');
        Route::match(['get', 'post'], '/kartu-pelajar', [\App\Modules\Printing\Controllers\PrintingWebController::class, 'kartuPelajar'])->name('kartu-pelajar');
        Route::match(['get', 'post'], '/kwitansi', [\App\Modules\Printing\Controllers\PrintingWebController::class, 'kwitansi'])->name('kwitansi');
        Route::match(['get', 'post'], '/legger', [\App\Modules\Printing\Controllers\PrintingWebController::class, 'leggerNilai'])->name('legger');
        Route::match(['get', 'post'], '/buku-induk', [\App\Modules\Printing\Controllers\BukuIndukController::class, 'bukuInduk'])->name('buku-induk');
        Route::match(['get', 'post'], '/ijazah', [\App\Modules\Printing\Controllers\IjazahController::class, 'ijazah'])->name('ijazah');
        Route::match(['get', 'post'], '/skhu', [\App\Modules\Printing\Controllers\IjazahController::class, 'skhu'])->name('skhu');
    });

    // Bimbingan Karir
    Route::prefix('career')->name('career.')->group(function () {
        Route::get('/', [\App\Modules\StudentLife\Career\Controllers\CareerWebController::class, 'index'])->name('index');
        Route::get('/student/{student}', [\App\Modules\StudentLife\Career\Controllers\CareerWebController::class, 'student'])->name('student');
        Route::post('/interest', [\App\Modules\StudentLife\Career\Controllers\CareerWebController::class, 'storeInterest'])->name('interest.store');
        Route::delete('/interest/{interest}', [\App\Modules\StudentLife\Career\Controllers\CareerWebController::class, 'deleteInterest'])->name('interest.delete');
        Route::post('/plan', [\App\Modules\StudentLife\Career\Controllers\CareerWebController::class, 'storePlan'])->name('plan.store');
        Route::delete('/plan/{plan}', [\App\Modules\StudentLife\Career\Controllers\CareerWebController::class, 'deletePlan'])->name('plan.delete');
    });

    // BKK / DUDI
    Route::prefix('bkk')->name('bkk.')->group(function () {
        Route::get('/', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'index'])->name('index');
        Route::get('/company/create', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'createCompany'])->name('create.company');
        Route::post('/company', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'storeCompany'])->name('store.company');
        Route::get('/company/{company}/edit', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'editCompany'])->name('edit.company');
        Route::put('/company/{company}', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'updateCompany'])->name('update.company');
        Route::delete('/company/{company}', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'destroyCompany'])->name('destroy.company');
        Route::get('/vacancy/create', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'createVacancy'])->name('create.vacancy');
        Route::post('/vacancy', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'storeVacancy'])->name('store.vacancy');
        Route::get('/vacancy/{vacancy}/edit', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'editVacancy'])->name('edit.vacancy');
        Route::put('/vacancy/{vacancy}', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'updateVacancy'])->name('update.vacancy');
        Route::delete('/vacancy/{vacancy}', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'destroyVacancy'])->name('destroy.vacancy');
        Route::get('/vacancy/{vacancy}/applications', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'applications'])->name('applications');
        Route::post('/vacancy/{vacancy}/apply', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'apply'])->name('apply');
        Route::put('/application/{application}/status', [\App\Modules\Bkk\Controllers\BkkWebController::class, 'updateApplicationStatus'])->name('update.application.status');
    });

    // UKK / Sertifikasi BNSP
    Route::prefix('ukk')->name('ukk.')->group(function () {
        Route::get('/', [\App\Modules\Ukk\Controllers\UkkWebController::class, 'index'])->name('index');
        Route::get('/schema/create', [\App\Modules\Ukk\Controllers\UkkWebController::class, 'createSchema'])->name('create.schema');
        Route::post('/schema', [\App\Modules\Ukk\Controllers\UkkWebController::class, 'storeSchema'])->name('store.schema');
        Route::get('/schema/{schema}/edit', [\App\Modules\Ukk\Controllers\UkkWebController::class, 'editSchema'])->name('edit.schema');
        Route::put('/schema/{schema}', [\App\Modules\Ukk\Controllers\UkkWebController::class, 'updateSchema'])->name('update.schema');
        Route::delete('/schema/{schema}', [\App\Modules\Ukk\Controllers\UkkWebController::class, 'destroySchema'])->name('destroy.schema');
        Route::get('/cert/create', [\App\Modules\Ukk\Controllers\UkkWebController::class, 'createCert'])->name('create.cert');
        Route::post('/cert', [\App\Modules\Ukk\Controllers\UkkWebController::class, 'storeCert'])->name('store.cert');
        Route::get('/cert/{cert}/edit', [\App\Modules\Ukk\Controllers\UkkWebController::class, 'editCert'])->name('edit.cert');
        Route::put('/cert/{cert}', [\App\Modules\Ukk\Controllers\UkkWebController::class, 'updateCert'])->name('update.cert');
        Route::delete('/cert/{cert}', [\App\Modules\Ukk\Controllers\UkkWebController::class, 'destroyCert'])->name('destroy.cert');
    });

    // Teaching Factory (TEFA)
    Route::prefix('tefa')->name('tefa.')->group(function () {
        Route::get('/', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'index'])->name('index');
        Route::get('/products', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'products'])->name('products');
        Route::get('/products/create', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'createProduct'])->name('create.product');
        Route::post('/products', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'storeProduct'])->name('store.product');
        Route::get('/products/{product}/edit', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'editProduct'])->name('edit.product');
        Route::put('/products/{product}', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'updateProduct'])->name('update.product');
        Route::delete('/products/{product}', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'destroyProduct'])->name('destroy.product');
        Route::get('/productions', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'productions'])->name('productions');
        Route::post('/productions', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'storeProduction'])->name('store.production');
        Route::delete('/productions/{production}', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'destroyProduction'])->name('destroy.production');
        Route::get('/sales', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'sales'])->name('sales');
        Route::get('/sales/create', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'createSale'])->name('create.sale');
        Route::post('/sales', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'storeSale'])->name('store.sale');
        Route::delete('/sales/{sale}', [\App\Modules\Tefa\Controllers\TefaWebController::class, 'destroySale'])->name('destroy.sale');
    });

    // Anggaran RKAS / BOS
    Route::prefix('budget')->name('budget.')->group(function () {
        Route::get('/', [\App\Modules\Budget\Controllers\BudgetWebController::class, 'index'])->name('index');
        Route::get('/category/create', [\App\Modules\Budget\Controllers\BudgetWebController::class, 'createCategory'])->name('create.category');
        Route::post('/category', [\App\Modules\Budget\Controllers\BudgetWebController::class, 'storeCategory'])->name('store.category');
        Route::get('/category/{category}/edit', [\App\Modules\Budget\Controllers\BudgetWebController::class, 'editCategory'])->name('edit.category');
        Route::put('/category/{category}', [\App\Modules\Budget\Controllers\BudgetWebController::class, 'updateCategory'])->name('update.category');
        Route::delete('/category/{category}', [\App\Modules\Budget\Controllers\BudgetWebController::class, 'destroyCategory'])->name('destroy.category');
        Route::get('/create', [\App\Modules\Budget\Controllers\BudgetWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Budget\Controllers\BudgetWebController::class, 'store'])->name('store');
        Route::get('/{budget}/edit', [\App\Modules\Budget\Controllers\BudgetWebController::class, 'edit'])->name('edit');
        Route::put('/{budget}', [\App\Modules\Budget\Controllers\BudgetWebController::class, 'update'])->name('update');
        Route::delete('/{budget}', [\App\Modules\Budget\Controllers\BudgetWebController::class, 'destroy'])->name('destroy');
    });

    // SPP Otomatis
    Route::prefix('spp')->name('spp.')->group(function () {
        Route::get('/', [\App\Modules\Finance\Controllers\SppWebController::class, 'index'])->name('index');
        Route::get('/generate', [\App\Modules\Finance\Controllers\SppWebController::class, 'generateForm'])->name('generate.form');
        Route::post('/generate', [\App\Modules\Finance\Controllers\SppWebController::class, 'generate'])->name('generate');
        Route::get('/student/{student}', [\App\Modules\Finance\Controllers\SppWebController::class, 'studentInvoices'])->name('student');
    });

    // Kenaikan Kelas
    Route::prefix('kenaikan')->name('kenaikan.')->group(function () {
        Route::get('/', [\App\Modules\Academic\Kenaikan\Controllers\KenaikanWebController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Academic\Kenaikan\Controllers\KenaikanWebController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Academic\Kenaikan\Controllers\KenaikanWebController::class, 'store'])->name('store');
        Route::post('/process', [\App\Modules\Academic\Kenaikan\Controllers\KenaikanWebController::class, 'process'])->name('process');
        Route::get('/{move}/edit', [\App\Modules\Academic\Kenaikan\Controllers\KenaikanWebController::class, 'edit'])->name('edit');
        Route::put('/{move}', [\App\Modules\Academic\Kenaikan\Controllers\KenaikanWebController::class, 'update'])->name('update');
        Route::delete('/{move}', [\App\Modules\Academic\Kenaikan\Controllers\KenaikanWebController::class, 'destroy'])->name('destroy');
    });
});

// Asrama
Route::prefix('asrama')->name('asrama.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Modules\Asrama\Controllers\AsramaWebController::class, 'index'])->name('index');
    Route::post('/dormitories', [\App\Modules\Asrama\Controllers\AsramaWebController::class, 'storeDormitory'])->name('dormitory.store');
    Route::put('/dormitories/{dormitory}', [\App\Modules\Asrama\Controllers\AsramaWebController::class, 'updateDormitory'])->name('dormitory.update');
    Route::delete('/dormitories/{dormitory}', [\App\Modules\Asrama\Controllers\AsramaWebController::class, 'deleteDormitory'])->name('dormitory.delete');
    Route::post('/rooms', [\App\Modules\Asrama\Controllers\AsramaWebController::class, 'storeRoom'])->name('room.store');
    Route::put('/rooms/{room}', [\App\Modules\Asrama\Controllers\AsramaWebController::class, 'updateRoom'])->name('room.update');
    Route::delete('/rooms/{room}', [\App\Modules\Asrama\Controllers\AsramaWebController::class, 'deleteRoom'])->name('room.delete');
    Route::post('/assignments', [\App\Modules\Asrama\Controllers\AsramaWebController::class, 'storeAssignment'])->name('assignment.store');
    Route::put('/assignments/{assignment}', [\App\Modules\Asrama\Controllers\AsramaWebController::class, 'updateAssignment'])->name('assignment.update');
    Route::delete('/assignments/{assignment}', [\App\Modules\Asrama\Controllers\AsramaWebController::class, 'deleteAssignment'])->name('assignment.delete');
});

// Transportasi
Route::prefix('transportasi')->name('transportasi.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Modules\Transportasi\Controllers\TransportasiWebController::class, 'index'])->name('index');
    Route::post('/routes', [\App\Modules\Transportasi\Controllers\TransportasiWebController::class, 'storeRoute'])->name('route.store');
    Route::put('/routes/{route}', [\App\Modules\Transportasi\Controllers\TransportasiWebController::class, 'updateRoute'])->name('route.update');
    Route::delete('/routes/{route}', [\App\Modules\Transportasi\Controllers\TransportasiWebController::class, 'deleteRoute'])->name('route.delete');
    Route::post('/vehicles', [\App\Modules\Transportasi\Controllers\TransportasiWebController::class, 'storeVehicle'])->name('vehicle.store');
    Route::put('/vehicles/{vehicle}', [\App\Modules\Transportasi\Controllers\TransportasiWebController::class, 'updateVehicle'])->name('vehicle.update');
    Route::delete('/vehicles/{vehicle}', [\App\Modules\Transportasi\Controllers\TransportasiWebController::class, 'deleteVehicle'])->name('vehicle.delete');
    Route::post('/students', [\App\Modules\Transportasi\Controllers\TransportasiWebController::class, 'storeStudent'])->name('student.store');
    Route::put('/students/{transportStudent}', [\App\Modules\Transportasi\Controllers\TransportasiWebController::class, 'updateStudent'])->name('student.update');
    Route::delete('/students/{transportStudent}', [\App\Modules\Transportasi\Controllers\TransportasiWebController::class, 'deleteStudent'])->name('student.delete');
});

// Payment Gateway
Route::prefix('payment')->name('payment.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Modules\Finance\PaymentGateway\Controllers\PaymentGatewayWebController::class, 'index'])->name('index');
    Route::get('/config', [\App\Modules\Finance\PaymentGateway\Controllers\PaymentGatewayWebController::class, 'config'])->name('config');
    Route::post('/config', [\App\Modules\Finance\PaymentGateway\Controllers\PaymentGatewayWebController::class, 'updateConfig'])->name('config.update');
    Route::post('/pay/{invoice}', [\App\Modules\Finance\PaymentGateway\Controllers\PaymentGatewayWebController::class, 'payInvoice'])->name('pay');
});
// Callback (no auth)
Route::post('/payment/callback', [\App\Modules\Finance\PaymentGateway\Controllers\PaymentGatewayWebController::class, 'callback'])->name('payment.callback');

// Kelas Industri
Route::prefix('industry')->name('industry.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'index'])->name('index');
    Route::get('/partners', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'partners'])->name('partners');
    Route::post('/partners', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'storePartner'])->name('partner.store');
    Route::put('/partners/{partner}', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'updatePartner'])->name('partner.update');
    Route::delete('/partners/{partner}', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'deletePartner'])->name('partner.delete');
    Route::get('/programs', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'programs'])->name('programs');
    Route::post('/programs', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'storeProgram'])->name('program.store');
    Route::put('/programs/{program}', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'updateProgram'])->name('program.update');
    Route::delete('/programs/{program}', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'deleteProgram'])->name('program.delete');
    Route::get('/students', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'students'])->name('students');
    Route::post('/students', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'assignStudent'])->name('student.assign');
    Route::put('/students/{industryStudent}', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'updateStudentStatus'])->name('student.update');
    Route::delete('/students/{industryStudent}', [\App\Modules\Industry\Controllers\IndustryWebController::class, 'removeStudent'])->name('student.remove');
});

require __DIR__ . '/modules/ppdb.php';
require __DIR__ . '/modules/koperasi.php';

// Dapodik Integration
Route::prefix('dapodik')->name('dapodik.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Modules\Dapodik\Controllers\DapodikWebController::class, 'index'])->name('index');
    Route::post('/sync/{type}', [\App\Modules\Dapodik\Controllers\DapodikWebController::class, 'sync'])->name('sync');
    Route::post('/config', [\App\Modules\Dapodik\Controllers\DapodikWebController::class, 'updateConfig'])->name('config');
});

// Kepegawaian / HR
Route::middleware('auth')->prefix('hr')->name('hr.')->group(function () {
    Route::get('/', [\App\Modules\HR\Controllers\HRWebController::class, 'index'])->name('index');
    Route::get('/attendance', [\App\Modules\HR\Controllers\HRWebController::class, 'attendance'])->name('attendance');
    Route::post('/checkin', [\App\Modules\HR\Controllers\HRWebController::class, 'checkIn'])->name('checkin');
    Route::post('/checkout/{attendance}', [\App\Modules\HR\Controllers\HRWebController::class, 'checkOut'])->name('checkout');
    Route::get('/leave', [\App\Modules\HR\Controllers\HRWebController::class, 'leave'])->name('leave');
    Route::post('/leave', [\App\Modules\HR\Controllers\HRWebController::class, 'storeLeave'])->name('leave.store');
    Route::post('/leave/{leave}/approve', [\App\Modules\HR\Controllers\HRWebController::class, 'approveLeave'])->name('leave.approve');
    Route::get('/performance', [\App\Modules\HR\Controllers\HRWebController::class, 'performance'])->name('performance');
    Route::post('/performance', [\App\Modules\HR\Controllers\HRWebController::class, 'storePerformance'])->name('performance.store');
    Route::get('/{user}', [\App\Modules\HR\Controllers\HRWebController::class, 'detail'])->name('detail');
});

// Manajemen Aset
Route::middleware('auth')->prefix('asset')->name('asset.')->group(function () {
    Route::get('/', [\App\Modules\Asset\Controllers\AssetWebController::class, 'index'])->name('index');
    Route::get('/categories', [\App\Modules\Asset\Controllers\AssetWebController::class, 'categories'])->name('categories');
    Route::post('/categories', [\App\Modules\Asset\Controllers\AssetWebController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [\App\Modules\Asset\Controllers\AssetWebController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [\App\Modules\Asset\Controllers\AssetWebController::class, 'destroyCategory'])->name('categories.destroy');
    Route::get('/list', [\App\Modules\Asset\Controllers\AssetWebController::class, 'assets'])->name('list');
    Route::get('/create', [\App\Modules\Asset\Controllers\AssetWebController::class, 'createAsset'])->name('create');
    Route::post('/', [\App\Modules\Asset\Controllers\AssetWebController::class, 'storeAsset'])->name('store');
    Route::get('/{asset}/edit', [\App\Modules\Asset\Controllers\AssetWebController::class, 'editAsset'])->name('edit');
    Route::put('/{asset}', [\App\Modules\Asset\Controllers\AssetWebController::class, 'updateAsset'])->name('update');
    Route::delete('/{asset}', [\App\Modules\Asset\Controllers\AssetWebController::class, 'destroyAsset'])->name('destroy');
    Route::get('/loans', [\App\Modules\Asset\Controllers\AssetWebController::class, 'loans'])->name('loans');
    Route::post('/loans', [\App\Modules\Asset\Controllers\AssetWebController::class, 'storeLoan'])->name('loans.store');
    Route::post('/loans/{loan}/return', [\App\Modules\Asset\Controllers\AssetWebController::class, 'returnLoan'])->name('loans.return');
    Route::get('/consumables', [\App\Modules\Asset\Controllers\AssetWebController::class, 'consumables'])->name('consumables');
    Route::post('/consumables', [\App\Modules\Asset\Controllers\AssetWebController::class, 'storeConsumable'])->name('consumables.store');
    Route::put('/consumables/{consumable}', [\App\Modules\Asset\Controllers\AssetWebController::class, 'updateConsumable'])->name('consumables.update');
    Route::delete('/consumables/{consumable}', [\App\Modules\Asset\Controllers\AssetWebController::class, 'destroyConsumable'])->name('consumables.destroy');
});
