@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('jadwal-ujian.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($ujian) ? 'Edit Jadwal Ujian' : 'Tambah Jadwal Ujian' }}</h1></div>
    <form method="POST" action="{{ isset($ujian) ? route('jadwal-ujian.update', $ujian) : route('jadwal-ujian.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($ujian)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Ujian <span class="text-red-500">*</span></label><input type="text" name="name" required value="{{ old('name', $ujian->name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('description', $ujian->description ?? '') }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Kelas <span class="text-red-500">*</span></label><select name="class_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih</option>@foreach($classes as $c)<option value="{{ $c->id }}" {{ old('class_id', $ujian->class_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Mapel <span class="text-red-500">*</span></label><select name="subject_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih</option>@foreach($subjects as $s)<option value="{{ $s->id }}" {{ old('subject_id', $ujian->subject_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Ujian <span class="text-red-500">*</span></label><input type="date" name="exam_date" required value="{{ old('exam_date', isset($ujian) ? $ujian->exam_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label><input type="time" name="start_time" required value="{{ old('start_time', $ujian->start_time ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label><input type="time" name="end_time" required value="{{ old('end_time', $ujian->end_time ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Ruangan</label><input type="text" name="room" value="{{ old('room', $ujian->room ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tipe <span class="text-red-500">*</span></label><select name="type" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="quiz" {{ old('type', $ujian->type ?? '') == 'quiz' ? 'selected' : '' }}>Quiz</option><option value="midterm" {{ old('type', $ujian->type ?? '') == 'midterm' ? 'selected' : '' }}>UTS</option><option value="final" {{ old('type', $ujian->type ?? '') == 'final' ? 'selected' : '' }}>UAS</option><option value="other" {{ old('type', $ujian->type ?? '') == 'other' ? 'selected' : '' }}>Lainnya</option></select></div>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($ujian) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
