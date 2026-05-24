@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Kelas Industri</h1><div class="flex gap-2"><a href="{{ route('industry.partners') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-handshake mr-2"></i>Mitra</a><a href="{{ route('industry.programs') }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700"><i class="fas fa-tasks mr-2"></i>Program</a><a href="{{ route('industry.students') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700"><i class="fas fa-user-graduate mr-2"></i>Siswa</a></div></div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Total Mitra</p><p class="text-3xl font-bold text-slate-900">{{ $partnersCount }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Program Aktif</p><p class="text-3xl font-bold text-amber-600">{{ $activePrograms }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Siswa Terdaftar</p><p class="text-3xl font-bold text-emerald-600">{{ $enrolledStudents }}</p></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Mitra Industri</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Nama</th><th class="text-left px-4 py-3 font-medium">Kontak</th><th class="text-left px-4 py-3 font-medium">Program</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($partners->take(8) as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                            <td class="px-4 py-3">{{ $p->phone ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $p->programs_count }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-slate-500 py-8">Belum ada mitra</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Program Industri</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Program</th><th class="text-left px-4 py-3 font-medium">Mitra</th><th class="text-left px-4 py-3 font-medium">Status</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($programs->take(8) as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                            <td class="px-4 py-3">{{ $p->partner->name ?? '-' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full {{ $p->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $p->status }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-slate-500 py-8">Belum ada program</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
