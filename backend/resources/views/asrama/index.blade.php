@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Manajemen Asrama</h1></div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Total Asrama</p><p class="text-3xl font-bold text-slate-900">{{ $dormitoryCount }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Total Kamar</p><p class="text-3xl font-bold text-slate-900">{{ $roomCount }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Siswa Aktif</p><p class="text-3xl font-bold text-emerald-600">{{ $activeAssignments }}</p></div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Daftar Asrama</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Nama</th><th class="text-left px-4 py-3 font-medium">Gender</th><th class="text-left px-4 py-3 font-medium">Kapasitas</th><th class="text-left px-4 py-3 font-medium">Kamar</th><th class="text-left px-4 py-3 font-medium">Supervisor</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dormitories as $d)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $d->name }}</td>
                        <td class="px-4 py-3">{{ ucfirst($d->gender) }}</td>
                        <td class="px-4 py-3">{{ $d->capacity }}</td>
                        <td class="px-4 py-3">{{ $d->rooms_count }}</td>
                        <td class="px-4 py-3">{{ $d->supervisor?->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-slate-500 py-8">Belum ada asrama</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
