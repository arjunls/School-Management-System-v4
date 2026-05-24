@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Halo, {{ $user->name }}!</h1>
        <span class="text-sm text-slate-500">{{ $user->kelas?->name ?? '-' }} | {{ $user->nisn ?? $user->id }}</span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div><p class="text-sm text-slate-500">Nilai</p><p class="text-2xl font-bold text-slate-900">{{ $totalGrades }}</p></div>
                <div class="bg-purple-50 p-3 rounded-lg"><i class="fas fa-list text-purple-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div><p class="text-sm text-slate-500">Hadir</p><p class="text-2xl font-bold text-green-600">{{ $totalHadir }}</p></div>
                <div class="bg-green-50 p-3 rounded-lg"><i class="fas fa-check-circle text-green-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div><p class="text-sm text-slate-500">Sakit / Izin</p><p class="text-2xl font-bold text-yellow-600">{{ $totalSakit + $totalIzin }}</p></div>
                <div class="bg-yellow-50 p-3 rounded-lg"><i class="fas fa-clock text-yellow-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div><p class="text-sm text-slate-500">Tagihan Lunas</p><p class="text-2xl font-bold text-slate-900">{{ $totalLunas }}/{{ $totalTagihan }}</p></div>
                <div class="bg-blue-50 p-3 rounded-lg"><i class="fas fa-credit-card text-blue-600 text-xl"></i></div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Nilai Terbaru</h2>
                    <a href="{{ route('siswa.portal.grades') }}" class="text-sm text-blue-600 hover:text-blue-800">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentGrades as $g)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                        <span class="text-sm text-slate-700">{{ $g->subject?->name ?? '-' }}</span>
                        <span class="px-2 py-0.5 rounded-lg text-sm font-semibold {{ $g->score >= 75 ? 'bg-green-100 text-green-800' : ($g->score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $g->score }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500">Belum ada nilai</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Kehadiran Terbaru</h2>
                    <a href="{{ route('siswa.portal.attendance') }}" class="text-sm text-blue-600 hover:text-blue-800">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse($latestAttendance as $a)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                        <span class="text-sm text-slate-700">{{ \Carbon\Carbon::parse($a->date)->format('d/m/Y') }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $a->status === 'hadir' ? 'bg-green-100 text-green-800' : ($a->status === 'sakit' ? 'bg-yellow-100 text-yellow-800' : ($a->status === 'izin' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">{{ ucfirst($a->status) }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-slate-500">Belum ada kehadiran</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('siswa.portal.grades') }}" class="flex-1 px-4 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors text-center"><i class="fas fa-list mr-2"></i>Nilai Saya</a>
        <a href="{{ route('siswa.portal.attendance') }}" class="flex-1 px-4 py-3 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition-colors text-center"><i class="fas fa-calendar-check mr-2"></i>Kehadiran</a>
        <a href="{{ route('siswa.portal.schedule') }}" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors text-center"><i class="fas fa-clock mr-2"></i>Jadwal</a>
        <a href="{{ route('siswa.portal.payments') }}" class="flex-1 px-4 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors text-center"><i class="fas fa-credit-card mr-2"></i>Tagihan</a>
    </div>
</div>
@endsection
