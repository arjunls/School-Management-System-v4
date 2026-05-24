@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('pkl.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($pkl) ? 'Edit Data PKL' : 'Tambah PKL / Prakerin' }}</h1></div>
    <form method="POST" action="{{ isset($pkl) ? route('pkl.update', $pkl) : route('pkl.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($pkl)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Siswa <span class="text-red-500">*</span></label>
            @if(isset($pkl))
            <p class="text-sm text-slate-900 py-2">{{ $pkl->student->name }}</p>
            @else
            <select name="student_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih Siswa</option>@foreach($students as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select>
            @endif
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Perusahaan <span class="text-red-500">*</span></label><input type="text" name="company_name" required value="{{ old('company_name', $pkl->company_name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Alamat Perusahaan</label><textarea name="company_address" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('company_address', $pkl->company_address ?? '') }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Pembimbing</label><input type="text" name="supervisor_name" value="{{ old('supervisor_name', $pkl->supervisor_name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">No. Telepon Pembimbing</label><input type="text" name="supervisor_phone" value="{{ old('supervisor_phone', $pkl->supervisor_phone ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label><input type="date" name="start_date" value="{{ old('start_date', isset($pkl) && $pkl->start_date ? $pkl->start_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Selesai</label><input type="date" name="end_date" value="{{ old('end_date', isset($pkl) && $pkl->end_date ? $pkl->end_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="active" {{ old('status', $pkl->status ?? 'active') == 'active' ? 'selected' : '' }}>Aktif</option><option value="completed" {{ old('status', $pkl->status ?? 'active') == 'completed' ? 'selected' : '' }}>Selesai</option><option value="extended" {{ old('status', $pkl->status ?? 'active') == 'extended' ? 'selected' : '' }}>Diperpanjang</option></select></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label><textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('notes', $pkl->notes ?? '') }}</textarea></div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($pkl) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
