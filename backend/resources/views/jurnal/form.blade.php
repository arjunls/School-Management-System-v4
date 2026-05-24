@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('jurnal.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($jurnal) ? 'Edit Jurnal' : 'Tambah Jurnal Mengajar' }}</h1></div>
    <form method="POST" action="{{ isset($jurnal) ? route('jurnal.update', $jurnal) : route('jurnal.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($jurnal)) @method('PUT') @endif
        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal <span class="text-red-500">*</span></label><input type="date" name="date" required value="{{ old('date', isset($jurnal) ? $jurnal->date->format('Y-m-d') : date('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jam Mulai</label><input type="time" name="start_time" value="{{ old('start_time', $jurnal->start_time ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jam Selesai</label><input type="time" name="end_time" value="{{ old('end_time', $jurnal->end_time ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Kelas <span class="text-red-500">*</span></label><select name="class_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih</option>@foreach($classes as $c)<option value="{{ $c->id }}" {{ old('class_id', $jurnal->class_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Mapel <span class="text-red-500">*</span></label><select name="subject_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih</option>@foreach($subjects as $s)<option value="{{ $s->id }}" {{ old('subject_id', $jurnal->subject_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Topik Pembelajaran</label><input type="text" name="topic" value="{{ old('topic', $jurnal->topic ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Materi</label><textarea name="material" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('material', $jurnal->material ?? '') }}</textarea></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label><textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('notes', $jurnal->notes ?? '') }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Siswa Hadir</label><input type="number" name="present_students" value="{{ old('present_students', $jurnal->present_students ?? '') }}" min="0" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Siswa Tidak Hadir</label><input type="number" name="absent_students" value="{{ old('absent_students', $jurnal->absent_students ?? '') }}" min="0" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($jurnal) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
