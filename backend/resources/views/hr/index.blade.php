@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Kepegawaian</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('hr.attendance') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-clipboard-check"></i>
                Absensi Hari Ini
            </a>
            <a href="{{ route('hr.leave') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-calendar-alt"></i>
                Pengajuan Cuti
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Guru</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalGuru) }}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg"><i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Hadir Hari Ini</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($hadirHariIni) }}</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg"><i class="fas fa-check-circle text-green-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Cuti Pending</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($cutiPending) }}</p>
                </div>
                <div class="bg-yellow-50 p-3 rounded-lg"><i class="fas fa-clock text-yellow-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Rata-rata Skor Evaluasi</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $rataSkor ? number_format($rataSkor, 1) : '-' }}</p>
                </div>
                <div class="bg-purple-50 p-3 rounded-lg"><i class="fas fa-star text-purple-600 text-xl"></i></div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-slate-900">Guru Terbaru</h2>
                    <a href="{{ route('guru.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse($guruTerbaru as $g)
                    <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-lg transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-medium">
                                {{ substr($g->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">{{ $g->name }}</p>
                                <p class="text-sm text-slate-500">{{ $g->email ?? '-' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('hr.detail', $g) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400">Belum ada data guru</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-slate-900">Evaluasi Terbaru</h2>
                    <a href="{{ route('hr.performance') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentEvaluations as $ev)
                    <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-lg transition-colors">
                        <div>
                            <p class="font-medium text-slate-900">{{ $ev->teacher->name ?? '-' }}</p>
                            <p class="text-sm text-slate-500">{{ $ev->type }} - {{ $ev->evaluation_date->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold {{ $ev->score >= 80 ? 'text-green-600' : ($ev->score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($ev->score) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400">Belum ada evaluasi</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
