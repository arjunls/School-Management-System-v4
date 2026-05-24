<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin Panel Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // Siswa
    Route::get('/siswa', function () {
        return view('siswa.index');
    })->name('siswa.index');

    // Guru
    Route::get('/guru', function () {
        return view('guru.index');
    })->name('guru.index');

    // Kelas
    Route::get('/kelas', function () {
        return view('kelas.index');
    })->name('kelas.index');

    // Kehadiran
    Route::get('/kehadiran', function () {
        return view('kehadiran.index');
    })->name('kehadiran.index');

    // Jadwal
    Route::get('/jadwal', function () {
        return view('jadwal.index');
    })->name('jadwal.index');

    // Nilai
    Route::get('/nilai', function () {
        return view('nilai.index');
    })->name('nilai.index');

    // Pembayaran
    Route::get('/pembayaran', function () {
        return view('pembayaran.index');
    })->name('pembayaran.index');

    // Laporan
    Route::get('/laporan', function () {
        return view('laporan.index');
    })->name('laporan.index');

    // Dokumen
    Route::get('/dokumen', function () {
        return view('dokumen.index');
    })->name('dokumen.index');
});
