@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('asset.assets') }}" class="text-slate-500 hover:text-slate-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-slate-900">{{ isset($asset) ? 'Edit Aset' : 'Tambah Aset' }}</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 max-w-3xl">
        <form action="{{ isset($asset) ? route('asset.update', $asset) : route('asset.store') }}" method="POST">
            @csrf
            @if(isset($asset)) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Nama Aset <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $asset->name ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Kode Aset</label>
                    <input type="text" name="code" value="{{ old('code', $asset->code ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $asset->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="available" @selected(old('status', $asset->status ?? '') === 'available')>Tersedia</option>
                        <option value="borrowed" @selected(old('status', $asset->status ?? '') === 'borrowed')>Dipinjam</option>
                        <option value="maintenance" @selected(old('status', $asset->status ?? '') === 'maintenance')>Perawatan</option>
                        <option value="retired" @selected(old('status', $asset->status ?? '') === 'retired')>Pensiun</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $asset->location ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Kondisi</label>
                    <input type="text" name="condition" value="{{ old('condition', $asset->condition ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Baik, Rusak Ringan, dll">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Harga Beli</label>
                    <input type="number" name="purchase_price" value="{{ old('purchase_price', $asset->purchase_price ?? '') }}" min="0" step="0.01" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal Beli</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', isset($asset) && $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $asset->description ?? '') }}</textarea>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('asset.assets') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 border border-slate-300 rounded-lg">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    {{ isset($asset) ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
