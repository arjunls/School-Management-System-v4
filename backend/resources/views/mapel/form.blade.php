@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ isset($mapel) ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran Baru' }}</h1>
        <a href="{{ route('mapel.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ isset($mapel) ? route('mapel.update', $mapel) : route('mapel.store') }}" method="POST" class="space-y-4">
            @csrf
            @isset($mapel) @method('PUT') @endisset

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kode Mapel</label>
                    <input type="text" name="code" value="{{ old('code', $mapel->code ?? '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Mapel</label>
                    <input type="text" name="name" value="{{ old('name', $mapel->name ?? '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">SKS</label>
                    <input type="number" name="credits" value="{{ old('credits', $mapel->credits ?? '') }}" min="1" max="20" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Guru Pengampu</label>
                    <select name="teacher_id" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        <option value="">Pilih Guru</option>
                        @foreach($teachers as $t)
                        <option value="{{ $t->id }}" @selected(old('teacher_id', $mapel->teacher_id ?? '') == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">{{ old('description', $mapel->description ?? '') }}</textarea>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition-colors">{{ isset($mapel) ? 'Perbarui' : 'Simpan' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
