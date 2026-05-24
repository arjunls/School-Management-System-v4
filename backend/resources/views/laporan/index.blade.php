@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('common.laporan') }}</h1>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Siswa</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalSiswa) }}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg"><i class="fas fa-user-graduate text-blue-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Guru</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalGuru) }}</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg"><i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Nilai</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalGrades) }}</p>
                </div>
                <div class="bg-purple-50 p-3 rounded-lg"><i class="fas fa-list text-purple-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Kehadiran</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalAttendance) }}</p>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg"><i class="fas fa-check-square text-orange-600 text-xl"></i></div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Rekapitulasi Kehadiran</h2>
            <div class="space-y-3">
                @php $colors = ['hadir' => 'bg-green-500', 'sakit' => 'bg-yellow-500', 'izin' => 'bg-blue-500', 'alpha' => 'bg-red-500']; @endphp
                @foreach(['hadir', 'sakit', 'izin', 'alpha'] as $s)
                @php $total = $rekapKehadiran[$s] ?? 0; $max = max($rekapKehadiran->sum(), 1); @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-slate-700">{{ ucfirst($s) }}</span>
                        <span class="text-slate-500">{{ $total }}</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="{{ $colors[$s] }} h-2 rounded-full" style="width: {{ ($total / $max) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Akses Laporan</h2>
            <div class="space-y-3">
                <a href="{{ route('laporan.attendance') }}" class="flex items-center justify-between px-4 py-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                    <span class="font-medium text-slate-700"><i class="fas fa-calendar-check mr-2 text-orange-500"></i>Kehadiran</span>
                    <i class="fas fa-chevron-right text-slate-400"></i>
                </a>
                <a href="{{ route('laporan.grades') }}" class="flex items-center justify-between px-4 py-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                    <span class="font-medium text-slate-700"><i class="fas fa-list mr-2 text-purple-500"></i>Nilai</span>
                    <i class="fas fa-chevron-right text-slate-400"></i>
                </a>
                <a href="{{ route('laporan.payments') }}" class="flex items-center justify-between px-4 py-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                    <span class="font-medium text-slate-700"><i class="fas fa-credit-card mr-2 text-green-500"></i>Pembayaran</span>
                    <i class="fas fa-chevron-right text-slate-400"></i>
                </a>
                <a href="{{ route('export.siswa') }}" class="flex items-center justify-between px-4 py-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                    <span class="font-medium text-slate-700"><i class="fas fa-download mr-2 text-blue-500"></i>Export Data Siswa (CSV)</span>
                    <i class="fas fa-chevron-right text-slate-400"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
