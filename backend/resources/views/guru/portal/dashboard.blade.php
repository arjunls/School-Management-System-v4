@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Dashboard Guru</h1>
        <span class="text-sm text-slate-500">{{ $user->name }}</span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div><p class="text-sm text-slate-500">Kelas Diampu</p><p class="text-2xl font-bold text-slate-900">{{ $allClasses->count() }}</p></div>
                <div class="bg-blue-50 p-3 rounded-lg"><i class="fas fa-chalkboard text-blue-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div><p class="text-sm text-slate-500">Total Siswa</p><p class="text-2xl font-bold text-slate-900">{{ $totalStudents }}</p></div>
                <div class="bg-green-50 p-3 rounded-lg"><i class="fas fa-users text-green-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div><p class="text-sm text-slate-500">Jadwal</p><p class="text-2xl font-bold text-slate-900">{{ $totalSchedules }}</p></div>
                <div class="bg-purple-50 p-3 rounded-lg"><i class="fas fa-calendar-alt text-purple-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div><p class="text-sm text-slate-500">Nilai Tercatat</p><p class="text-2xl font-bold text-slate-900">{{ $totalGrades }}</p></div>
                <div class="bg-orange-50 p-3 rounded-lg"><i class="fas fa-list text-orange-600 text-xl"></i></div>
            </div>
        </div>
    </div>

    @if($homeroomClasses->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Kelas Wali</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($homeroomClasses as $k)
            <div class="bg-slate-50 rounded-xl p-4">
                <h3 class="font-semibold text-slate-900">{{ $k->name }}</h3>
                <p class="text-sm text-slate-500">{{ $k->students->count() }} siswa</p>
                <div class="flex gap-2 mt-3">
                    <a href="{{ route('guru.portal.grades', $k) }}" class="text-xs px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200">Nilai</a>
                    <a href="{{ route('guru.portal.attendance', $k) }}" class="text-xs px-3 py-1 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200">Absensi</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Semua Kelas Diampu</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($allClasses as $k)
            <div class="border border-slate-200 rounded-xl p-4">
                <h3 class="font-semibold text-slate-900">{{ $k->name }}</h3>
                <p class="text-sm text-slate-500">{{ $k->students()->count() }} siswa</p>
                <div class="flex gap-2 mt-3">
                    <a href="{{ route('guru.portal.schedule') }}" class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">Jadwal</a>
                    <a href="{{ route('guru.portal.grades', $k) }}" class="text-xs px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200">Nilai</a>
                    <a href="{{ route('guru.portal.attendance', $k) }}" class="text-xs px-3 py-1 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200">Absensi</a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-slate-500 py-8">Belum ada kelas</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
