@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('tugas.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($tugas) ? 'Edit Tugas' : 'Buat Tugas Baru' }}</h1></div>
    <form method="POST" action="{{ isset($tugas) ? route('tugas.update', $tugas) : route('tugas.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($tugas)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Judul <span class="text-red-500">*</span></label><input type="text" name="title" required value="{{ old('title', $tugas->title ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="4" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('description', $tugas->description ?? '') }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Kelas <span class="text-red-500">*</span></label><select name="class_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih</option>@foreach($classes as $c)<option value="{{ $c->id }}" {{ old('class_id', $tugas->class_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Mapel <span class="text-red-500">*</span></label><select name="subject_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih</option>@foreach($subjects as $s)<option value="{{ $s->id }}" {{ old('subject_id', $tugas->subject_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tenggat <span class="text-red-500">*</span></label><input type="datetime-local" name="due_date" required value="{{ old('due_date', isset($tugas) ? $tugas->due_date->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nilai Maksimal</label><input type="number" name="max_score" value="{{ old('max_score', $tugas->max_score ?? 100) }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Lampiran (maks 10MB)</label><input type="file" name="attachment" class="w-full rounded-lg border border-slate-300 px-4 py-2"><p class="text-xs text-slate-400 mt-1">Format: PDF, DOC, DOCX, ZIP</p></div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($tugas) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
