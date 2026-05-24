@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Laporan Baru
            </button>
            <a href="{{ route('export.siswa') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-download"></i>
                Export Data
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Students Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Siswa</p>
                    <p class="text-2xl font-bold text-slate-900">1.245</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg">
                    <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-3 text-sm">
                <div class="flex-1">
                    <p class="text-slate-500">Bulan Ini</p>
                    <p class="font-medium text-slate-900">+12%</p>
                </div>
                <div class="w-0.5 bg-slate-200"></div>
                <div>
                    <p class="text-slate-500">Aktif</p>
                    <p class="font-medium text-slate-900">1.180</p>
                </div>
            </div>
        </div>

        <!-- Teachers Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Guru</p>
                    <p class="text-2xl font-bold text-slate-900">85</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-3 text-sm">
                <div class="flex-1">
                    <p class="text-slate-500">Bulan Ini</p>
                    <p class="font-medium text-slate-900">+3%</p>
                </div>
                <div class="w-0.5 bg-slate-200"></div>
                <div>
                    <p class="text-slate-500">Aktif</p>
                    <p class="font-medium text-slate-900">82</p>
                </div>
            </div>
        </div>

        <!-- Classes Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Kelas</p>
                    <p class="text-2xl font-bold text-slate-900">42</p>
                </div>
                <div class="bg-purple-50 p-3 rounded-lg">
                    <i class="fas fa-chalkboard text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-3 text-sm">
                <div class="flex-1">
                    <p class="text-slate-500">Bulan Ini</p>
                    <p class="font-medium text-slate-900">+2</p>
                </div>
                <div class="w-0.5 bg-slate-200"></div>
                <div>
                    <p class="text-slate-500">Berlangsung</p>
                    <p class="font-medium text-slate-900">38</p>
                </div>
            </div>
        </div>

        <!-- Attendance Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Tingkat Kehadiran</p>
                    <p class="text-2xl font-bold text-slate-900">94,5%</p>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg">
                    <i class="fas fa-check-square text-orange-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-3 text-sm">
                <div class="flex-1">
                    <p class="text-slate-500">Hari Ini</p>
                    <p class="font-medium text-slate-900">+2,1%</p>
                </div>
                <div class="w-0.5 bg-slate-200"></div>
                <div>
                    <p class="text-slate-500">Target</p>
                    <p class="font-medium text-slate-900">95%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid gap-6 xl:grid-cols-3">
        <!-- Chart: Monthly Enrollment Trend -->
        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Tren Pendaftaran Siswa</h2>
                <div class="relative" style="height:280px">
                    <canvas id="enrollmentChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart: Attendance Distribution -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Distribusi Kehadiran</h2>
                <div class="relative flex justify-center" style="height:280px">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts: Grade Distribution & Recent Activity -->
    <div class="grid gap-6 xl:grid-cols-3">
        <!-- Chart: Grade Distribution -->
        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Distribusi Nilai per Mata Pelajaran</h2>
                <div class="relative" style="height:280px">
                    <canvas id="gradeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-slate-900">Aktivitas Terbaru</h2>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Lihat Semua
                    </a>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="bg-blue-50 p-2 rounded-lg flex-shrink-0">
                            <i class="fas fa-user-graduate text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Siswa baru mendaftar: Ahmad Ramadhan</p>
                            <p class="text-sm text-slate-500">2 menit yang lalu</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="bg-green-50 p-2 rounded-lg flex-shrink-0">
                            <i class="fas fa-check-square text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Absensi diperbarui untuk XII RPL 1</p>
                            <p class="text-sm text-slate-500">15 menit yang lalu</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="bg-purple-50 p-2 rounded-lg flex-shrink-0">
                            <i class="fas fa-calendar-alt text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Jadwal baru diterbitkan</p>
                            <p class="text-sm text-slate-500">1 jam yang lalu</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="bg-orange-50 p-2 rounded-lg flex-shrink-0">
                            <i class="fas fa-credit-card text-orange-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Pembayaran diterima: Rp 1.200.000</p>
                            <p class="text-sm text-slate-500">2 jam yang lalu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and Upcoming Events -->
    <div class="grid gap-6 sm:grid-cols-2">
        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Aksi Cepat</h2>
                <div class="space-y-3">
                    <button class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-user-plus mr-3 text-blue-600"></i>
                        <span>Tambah Siswa Baru</span>
                    </button>
                    <button class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-chalkboard-teacher mr-3 text-green-600"></i>
                        <span>Tambah Guru Baru</span>
                    </button>
                    <button class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-calendar-plus mr-3 text-purple-600"></i>
                        <span>Buat Kelas Baru</span>
                    </button>
                    <button class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-clipboard-list mr-3 text-orange-600"></i>
                        <span>Ambil Absensi</span>
                    </button>
                    <button class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-file-alt mr-3 text-red-600"></i>
                        <span>Generate Laporan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Acara Mendatang</h2>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Pertemuan Orang Tua & Wali Kelas</p>
                            <p class="text-sm text-slate-500">Besok, 10:00 WIB</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Pameran Sains</p>
                            <p class="text-sm text-slate-500">Jumat Depan</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-purple-600 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Libur Semester</p>
                            <p class="text-sm text-slate-500">20 Desember</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-orange-600 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Latihan Wisuda</p>
                            <p class="text-sm text-slate-500">25 Mei</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Enrollment Trend Chart (Bar)
    const ctx1 = document.getElementById('enrollmentChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [{
                label: 'Pendaftar Baru',
                data: [45, 52, 38, 60, 48, 72, 55, 62, 70, 58, 64, 80],
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.06)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Attendance Distribution Chart (Doughnut)
    const ctx2 = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alpha'],
            datasets: [{
                data: [85, 7, 5, 3],
                backgroundColor: ['#22c55e', '#f59e0b', '#3b82f6', '#ef4444'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, usePointStyle: true }
                }
            },
            cutout: '70%'
        }
    });

    // Grade Distribution Chart (Line)
    const ctx3 = document.getElementById('gradeChart').getContext('2d');
    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: ['Matematika', 'Fisika', 'Kimia', 'B. Inggris', 'RPL', 'B. Indo', 'Sejarah'],
            datasets: [{
                label: 'Rata-rata Nilai',
                data: [78, 75, 72, 85, 88, 80, 76],
                borderColor: 'rgb(139, 92, 246)',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(139, 92, 246)',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    grid: { color: 'rgba(0,0,0,0.06)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush