@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ isset($kelas) ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</h1>
        <a href="{{ route('kelas.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ isset($kelas) ? route('kelas.update', $kelas) : route('kelas.store') }}" method="POST" class="space-y-4">
            @csrf
            @isset($kelas) @method('PUT') @endisset

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kelas</label>
                <input type="text" name="name" value="{{ old('name', $kelas->name ?? '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tingkat</label>
                <select name="grade_level" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Pilih</option>
                    @foreach(['X', 'XI', 'XII'] as $level)
                    <option value="{{ $level }}" @selected(old('grade_level', $kelas->grade_level ?? '') === $level)>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Wali Kelas</label>
                <select name="homeroom_teacher_id" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Pilih Guru</option>
                    @foreach($guru as $g)
                    <option value="{{ $g->id }}" @selected(old('homeroom_teacher_id', $kelas->homeroom_teacher_id ?? '') == $g->id)>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kapasitas</label>
                <input type="number" name="capacity" value="{{ old('capacity', $kelas->capacity ?? '') }}" min="1" max="50" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">{{ isset($kelas) ? 'Perbarui' : 'Simpan' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
