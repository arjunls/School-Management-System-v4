@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Manajemen Transportasi</h1></div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Total Rute</p><p class="text-3xl font-bold text-slate-900">{{ $routeCount }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Total Kendaraan</p><p class="text-3xl font-bold text-slate-900">{{ $vehicleCount }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Siswa Aktif</p><p class="text-3xl font-bold text-emerald-600">{{ $activeStudentCount }}</p></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Rute Transportasi</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Nama</th><th class="text-left px-4 py-3 font-medium">Penjemputan</th><th class="text-left px-4 py-3 font-medium">Tujuan</th><th class="text-left px-4 py-3 font-medium">Siswa</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($routes as $r)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $r->name }}</td>
                            <td class="px-4 py-3">{{ $r->pickup_point }}</td>
                            <td class="px-4 py-3">{{ $r->dropoff_point }}</td>
                            <td class="px-4 py-3">{{ $r->students_count }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-slate-500 py-8">Belum ada rute</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Kendaraan</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Nama</th><th class="text-left px-4 py-3 font-medium">Plat</th><th class="text-left px-4 py-3 font-medium">Kapasitas</th><th class="text-left px-4 py-3 font-medium">Status</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($vehicles as $v)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $v->name }}</td>
                            <td class="px-4 py-3">{{ $v->plate_number }}</td>
                            <td class="px-4 py-3">{{ $v->capacity }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full {{ $v->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($v->status === 'maintenance' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">{{ ucfirst($v->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-slate-500 py-8">Belum ada kendaraan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
