@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('ukk.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($schema) ? 'Edit Skema' : 'Tambah Skema Sertifikasi' }}</h1></div>
    <form method="POST" action="{{ isset($schema) ? route('ukk.update.schema', $schema) : route('ukk.store.schema') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($schema)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Skema <span class="text-red-500">*</span></label><input type="text" name="name" required value="{{ old('name', $schema->name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Bidang</label><input type="text" name="field" value="{{ old('field', $schema->field ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Level</label><input type="text" name="level" value="{{ old('level', $schema->level ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('description', $schema->description ?? '') }}</textarea></div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($schema) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
