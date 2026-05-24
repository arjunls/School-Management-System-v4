@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}</h1>
        <a href="{{ route('koperasi.products') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ isset($product) ? route('koperasi.products.update', $product) : route('koperasi.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @isset($product) @method('PUT') @endisset

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Produk</label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $product->description ?? '') }}</textarea>
                @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Harga</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-medium">Rp</span>
                        <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" min="0" required class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Stok</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? '') }}" min="0" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('stock') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Satuan</label>
                    <select name="unit" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        @foreach(['Pcs', 'Buah', 'Bungkus', 'Botol', 'Kaleng', 'Paket', 'Lembar', 'Liter', 'Kg', 'Gram'] as $u)
                        <option value="{{ $u }}" @selected(old('unit', $product->unit ?? '') === $u)>{{ $u }}</option>
                        @endforeach
                    </select>
                    @error('unit') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                    <select name="category" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        @foreach(['Makanan', 'Minuman', 'Alat Tulis', 'ATK', 'Seragam', 'Aksesoris', 'Lainnya'] as $c)
                        <option value="{{ $c }}" @selected(old('category', $product->category ?? '') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Gambar Produk</label>
                @if(isset($product) && $product->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover rounded-lg">
                </div>
                @endif
                <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-slate-500 mt-1">Format: JPEG, PNG, JPG, GIF, WebP. Maksimal 2MB.</p>
                @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="active" @selected(old('status', $product->status ?? '') === 'active')>Aktif</option>
                    <option value="inactive" @selected(old('status', $product->status ?? '') === 'inactive')>Nonaktif</option>
                </select>
                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    {{ isset($product) ? 'Perbarui' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
