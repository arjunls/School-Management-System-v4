@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('kuis.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($kuis) ? 'Edit Kuis' : 'Buat Kuis Baru' }}</h1></div>
    <form method="POST" action="{{ isset($kuis) ? route('kuis.update', $kuis) : route('kuis.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($kuis)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Judul <span class="text-red-500">*</span></label><input type="text" name="title" required value="{{ old('title', $kuis->title ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('description', $kuis->description ?? '') }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Kelas <span class="text-red-500">*</span></label><select name="class_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih</option>@foreach($classes as $c)<option value="{{ $c->id }}" {{ old('class_id', $kuis->class_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Mapel <span class="text-red-500">*</span></label><select name="subject_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih</option>@foreach($subjects as $s)<option value="{{ $s->id }}" {{ old('subject_id', $kuis->subject_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select></div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Batas Waktu (menit)</label><input type="number" name="time_limit" value="{{ old('time_limit', $kuis->time_limit ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nilai Lulus</label><input type="number" name="passing_score" value="{{ old('passing_score', $kuis->passing_score ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="draft" {{ old('status', $kuis->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option><option value="published" {{ old('status', $kuis->status ?? 'draft') == 'published' ? 'selected' : '' }}>Published</option></select></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Tenggat</label><input type="datetime-local" name="due_date" value="{{ old('due_date', isset($kuis) && $kuis->due_date ? $kuis->due_date->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($kuis) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
