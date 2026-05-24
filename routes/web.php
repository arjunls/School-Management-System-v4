<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Login route (for auth middleware redirect)
Route::get('/login', function () {
    return view('welcome');
})->name('login');

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

    // Jadwal
    Route::get('/jadwal', function () {
        return view('jadwal.index');
    })->name('jadwal.index')->middleware('role:permission:view-jadwal');

    // Nilai
    Route::get('/nilai', function () {
        return view('nilai.index');
    })->name('nilai.index')->middleware('role:permission:view-nilai');

    // Pembayaran
    Route::get('/pembayaran', function () {
        return view('pembayaran.index');
    })->name('pembayaran.index')->middleware('role:permission:view-pembayaran');

    // Laporan
    Route::get('/laporan', function () {
        return view('laporan.index');
    })->name('laporan.index')->middleware('role:permission:view-laporan');

    // Dokumen
    Route::get('/dokumen', function () {
        return view('dokumen.index');
    })->name('dokumen.index')->middleware('role:permission:view-dokumen');
});
