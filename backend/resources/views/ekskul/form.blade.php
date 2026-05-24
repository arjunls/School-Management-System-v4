@extends('layouts.app')
@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('ekskul.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($ekskul) ? 'Edit' : 'Tambah' }} Ekstrakurikuler</h1></div>
    <form method="POST" action="{{ isset($ekskul) ? route('ekskul.update', $ekskul) : route('ekskul.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($ekskul)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama <span class="text-red-500">*</span></label><input type="text" name="name" required value="{{ old('name', $ekskul->name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500">{{ old('description', $ekskul->description ?? '') }}</textarea></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Pembina</label><input type="text" name="coach" value="{{ old('coach', $ekskul->coach ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Jadwal</label><input type="text" name="schedule" value="{{ old('schedule', $ekskul->schedule ?? '') }}" placeholder="Senin 15:00-17:00" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Lokasi</label><input type="text" name="location" value="{{ old('location', $ekskul->location ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Maks. Peserta</label><input type="number" name="max_participants" value="{{ old('max_participants', $ekskul->max_participants ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="is_active" class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="1" {{ old('is_active', $ekskul->is_active ?? true) == 1 ? 'selected' : '' }}>Aktif</option><option value="0" {{ old('is_active', $ekskul->is_active ?? true) == 0 ? 'selected' : '' }}>Nonaktif</option></select></div>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($ekskul) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
