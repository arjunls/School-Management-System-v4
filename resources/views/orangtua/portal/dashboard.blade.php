@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-900">Halo, {{ $user->name }}!</h1>

    @forelse($children as $child)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $child->name }}</h2>
                    <p class="text-sm text-slate-500">{{ $child->nisn ?? '-' }} | {{ $child->kelas?->name ?? '-' }}</p>
                </div>
                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold">{{ $child->pivot->relationship ?? 'Anak' }}</span>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 mb-4">
                <div class="bg-purple-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500">Nilai</p>
                    <p class="text-xl font-bold text-slate-900">{{ $childStats[$child->id]['totalGrades'] }}</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500">Hadir</p>
                    <p class="text-xl font-bold text-green-600">{{ $childStats[$child->id]['totalHadir'] }}</p>
                </div>
                <div class="bg-yellow-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500">Sakit/Izin</p>
                    <p class="text-xl font-bold text-yellow-600">{{ $childStats[$child->id]['totalSakit'] + $childStats[$child->id]['totalIzin'] }}</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500">Tagihan Lunas</p>
                    <p class="text-xl font-bold text-slate-900">{{ $childStats[$child->id]['totalLunas'] }}/{{ $childStats[$child->id]['totalTagihan'] }}</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 mb-2">Nilai Terbaru</h3>
                    <div class="space-y-1">
                        @forelse($childStats[$child->id]['recentGrades'] as $g)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">{{ $g->subject?->name ?? '-' }}</span>
                            <span class="font-semibold {{ $g->score >= 75 ? 'text-green-600' : ($g->score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">{{ $g->score }}</span>
                        </div>
                        @empty
                        <p class="text-sm text-slate-400">Belum ada nilai</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 mb-2">Kehadiran Terbaru</h3>
                    <div class="space-y-1">
                        @forelse($childStats[$child->id]['latestAttendance'] as $a)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">{{ \Carbon\Carbon::parse($a->date)->format('d/m/Y') }}</span>
                            <span class="font-semibold {{ $a->status === 'hadir' ? 'text-green-600' : ($a->status === 'sakit' ? 'text-yellow-600' : ($a->status === 'izin' ? 'text-blue-600' : 'text-red-600')) }}">{{ ucfirst($a->status) }}</span>
                        </div>
                        @empty
                        <p class="text-sm text-slate-400">Belum ada kehadiran</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('orangtua.portal.grades', $child->id) }}" class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors text-sm"><i class="fas fa-list mr-1"></i>Nilai</a>
                <a href="{{ route('orangtua.portal.attendance', $child->id) }}" class="px-4 py-2 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition-colors text-sm"><i class="fas fa-calendar-check mr-1"></i>Kehadiran</a>
                <a href="{{ route('orangtua.portal.schedule', $child->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors text-sm"><i class="fas fa-clock mr-1"></i>Jadwal</a>
                <a href="{{ route('orangtua.portal.payments', $child->id) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors text-sm"><i class="fas fa-credit-card mr-1"></i>Tagihan</a>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
        <p class="text-slate-500">Belum ada anak terdaftar. Hubungi administrator untuk menghubungkan akun Anda.</p>
    </div>
    @endforelse
</div>
@endsection
