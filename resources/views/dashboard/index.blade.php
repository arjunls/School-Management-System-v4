@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('dashboard.title') }}</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('laporan.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                {{ __('dashboard.laporan_baru') }}
            </a>
            <a href="{{ route('export.siswa') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-download"></i>
                {{ __('dashboard.export_data') }}
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.total_siswa') }}</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalSiswa) }}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg"><i class="fas fa-user-graduate text-blue-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.total_guru') }}</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalGuru) }}</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg"><i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.total_kelas') }}</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalKelas) }}</p>
                </div>
                <div class="bg-purple-50 p-3 rounded-lg"><i class="fas fa-chalkboard text-purple-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.tingkat_kehadiran') }}</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($attendanceRate, 1) }}%</p>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg"><i class="fas fa-check-square text-orange-600 text-xl"></i></div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">{{ __('dashboard.tren_pendaftaran') }}</h2>
                <div class="relative" style="height:280px"><canvas id="enrollmentChart"></canvas></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">{{ __('dashboard.distribusi_kehadiran') }}</h2>
                <div class="relative flex justify-center" style="height:280px"><canvas id="attendanceChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Grade Chart + Activity -->
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">{{ __('dashboard.distribusi_nilai') }}</h2>
                <div class="relative" style="height:280px"><canvas id="gradeChart"></canvas></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-slate-900">{{ __('dashboard.aktivitas_terbaru') }}</h2>
                    <a href="{{ route('laporan.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">{{ __('dashboard.lihat_semua') }}</a>
                </div>
                <div class="space-y-4">
                    @foreach($recentActivities as $act)
                    <div class="flex items-start space-x-3">
                        <div class="bg-{{ $act['color'] }}-50 p-2 rounded-lg flex-shrink-0">
                            <i class="fas fa-{{ $act['icon'] }} text-{{ $act['color'] }}-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">{{ $act['text'] }}</p>
                            <p class="text-sm text-slate-500">{{ $act['time'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions + Events -->
    <div class="grid gap-6 sm:grid-cols-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">{{ __('dashboard.aksi_cepat') }}</h2>
                <div class="space-y-3">
                    <a href="{{ route('siswa.create') }}" class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-user-plus mr-3 text-blue-600"></i><span>{{ __('dashboard.tambah_siswa') }}</span>
                    </a>
                    <a href="{{ route('guru.create') }}" class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-chalkboard-teacher mr-3 text-green-600"></i><span>{{ __('dashboard.tambah_guru') }}</span>
                    </a>
                    <a href="{{ route('kelas.create') }}" class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-calendar-plus mr-3 text-purple-600"></i><span>{{ __('dashboard.buat_kelas') }}</span>
                    </a>
                    <a href="{{ route('qr.scanner') }}" class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-clipboard-list mr-3 text-orange-600"></i><span>{{ __('dashboard.ambil_absensi') }}</span>
                    </a>
                    <a href="{{ route('laporan.index') }}" class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-file-alt mr-3 text-red-600"></i><span>{{ __('dashboard.generate_laporan') }}</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">{{ __('dashboard.acara_mendatang') }}</h2>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0"><div class="w-3 h-3 bg-blue-600 rounded-full"></div></div>
                        <div class="flex-1"><p class="font-medium text-slate-900">Pertemuan Orang Tua & Wali Kelas</p><p class="text-sm text-slate-500">Besok, 10:00 WIB</p></div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0"><div class="w-3 h-3 bg-green-600 rounded-full"></div></div>
                        <div class="flex-1"><p class="font-medium text-slate-900">Pameran Sains</p><p class="text-sm text-slate-500">Jumat Depan</p></div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0"><div class="w-3 h-3 bg-purple-600 rounded-full"></div></div>
                        <div class="flex-1"><p class="font-medium text-slate-900">Libur Semester</p><p class="text-sm text-slate-500">20 Desember</p></div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0"><div class="w-3 h-3 bg-orange-600 rounded-full"></div></div>
                        <div class="flex-1"><p class="font-medium text-slate-900">Latihan Wisuda</p><p class="text-sm text-slate-500">25 Mei</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Chart(document.getElementById('enrollmentChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Pendaftar Baru',
                data: {!! json_encode($enrollmentData) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.06)' } }, x: { grid: { display: false } } }
        }
    });

    new Chart(document.getElementById('attendanceChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['{{ __("dashboard.hadir") }}', '{{ __("dashboard.sakit") }}', '{{ __("dashboard.izin") }}', '{{ __("dashboard.alpha") }}'],
            datasets: [{
                data: {!! json_encode(array_map(fn($v) => max($v, 0), $attendanceDist)) !!},
                backgroundColor: ['#22c55e', '#f59e0b', '#3b82f6', '#ef4444'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } } },
            cutout: '70%'
        }
    });

    @if(count($gradeLabels) > 0)
    new Chart(document.getElementById('gradeChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($gradeLabels) !!},
            datasets: [{
                label: 'Rata-rata Nilai',
                data: {!! json_encode($gradeData) !!},
                borderColor: 'rgb(139, 92, 246)',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(139, 92, 246)',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { min: 0, max: 100, grid: { color: 'rgba(0,0,0,0.06)' } }, x: { grid: { display: false } } }
        }
    });
    @endif
});
</script>
@endpush
