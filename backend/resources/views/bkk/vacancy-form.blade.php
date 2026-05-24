@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('bkk.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($vacancy) ? 'Edit Lowongan' : 'Tambah Lowongan' }}</h1></div>
    <form method="POST" action="{{ isset($vacancy) ? route('bkk.update.vacancy', $vacancy) : route('bkk.store.vacancy') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($vacancy)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Perusahaan <span class="text-red-500">*</span></label>
            <select name="company_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2">
                <option value="">Pilih Perusahaan</option>
                @foreach($companies as $c)
                <option value="{{ $c->id }}" {{ old('company_id', $vacancy->company_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Judul Lowongan <span class="text-red-500">*</span></label><input type="text" name="title" required value="{{ old('title', $vacancy->title ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('description', $vacancy->description ?? '') }}</textarea></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Persyaratan</label><textarea name="requirements" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('requirements', $vacancy->requirements ?? '') }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Slot</label><input type="number" name="slots" min="1" value="{{ old('slots', $vacancy->slots ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Tutup</label><input type="date" name="closing_date" value="{{ old('closing_date', isset($vacancy) && $vacancy->closing_date ? $vacancy->closing_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="open" {{ old('status', $vacancy->status ?? 'open') == 'open' ? 'selected' : '' }}>Terbuka</option><option value="closed" {{ old('status', $vacancy->status ?? 'open') == 'closed' ? 'selected' : '' }}>Ditutup</option></select></div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($vacancy) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
