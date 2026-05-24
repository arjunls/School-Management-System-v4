@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ isset($period) ? 'Edit Periode PPDB' : 'Tambah Periode PPDB' }}</h1>
        <a href="{{ route('ppdb.admin.periods') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ isset($period) ? route('ppdb.admin.periods.update', $period) : route('ppdb.admin.periods.store') }}" method="POST" class="space-y-4">
            @csrf
            @isset($period) @method('PUT') @endisset

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Periode</label>
                <input type="text" name="name" value="{{ old('name', $period->name ?? '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Ajaran</label>
                <input type="text" name="academic_year" value="{{ old('academic_year', $period->academic_year ?? '') }}" placeholder="contoh: 2025/2026" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('academic_year') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ old('start_date', isset($period) ? $period->start_date->format('Y-m-d') : '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('start_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ old('end_date', isset($period) ? $period->end_date->format('Y-m-d') : '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('end_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kuota Pendaftar</label>
                <input type="number" name="quota" value="{{ old('quota', $period->quota ?? '') }}" min="1" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('quota') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="inactive" @selected(old('status', $period->status ?? '') === 'inactive')>Tidak Aktif</option>
                    <option value="active" @selected(old('status', $period->status ?? '') === 'active')>Aktif</option>
                    <option value="closed" @selected(old('status', $period->status ?? '') === 'closed')>Ditutup</option>
                </select>
                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    {{ isset($period) ? 'Perbarui' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
