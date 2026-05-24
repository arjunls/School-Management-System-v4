@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('p5.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($p5) ? 'Edit Projek P5' : 'Buat Projek P5' }}</h1></div>
    <form method="POST" action="{{ isset($p5) ? route('p5.update', $p5) : route('p5.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($p5)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Judul <span class="text-red-500">*</span></label><input type="text" name="title" required value="{{ old('title', $p5->title ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('description', $p5->description ?? '') }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Kelas <span class="text-red-500">*</span></label><select name="class_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih</option>@foreach($classes as $c)<option value="{{ $c->id }}" {{ old('class_id', $p5->class_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="planned" {{ old('status', $p5->status ?? 'planned') == 'planned' ? 'selected' : '' }}>Direncanakan</option><option value="in_progress" {{ old('status', $p5->status ?? 'planned') == 'in_progress' ? 'selected' : '' }}>Berlangsung</option><option value="completed" {{ old('status', $p5->status ?? 'planned') == 'completed' ? 'selected' : '' }}>Selesai</option></select></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Tema</label><input type="text" name="theme" value="{{ old('theme', $p5->theme ?? '') }}" placeholder="Gaya Hidup Berkelanjutan, Kearifan Lokal, dll" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Dimensi</label><input type="text" name="dimension" value="{{ old('dimension', $p5->dimension ?? '') }}" placeholder="Beriman, Berkebinekaan Global, Gotong Royong, dll" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label><input type="date" name="start_date" value="{{ old('start_date', isset($p5) && $p5->start_date ? $p5->start_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Selesai</label><input type="date" name="end_date" value="{{ old('end_date', isset($p5) && $p5->end_date ? $p5->end_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($p5) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
