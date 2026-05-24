@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('tefa.products') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}</h1></div>
    <form method="POST" action="{{ isset($product) ? route('tefa.update.product', $product) : route('tefa.store.product') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($product)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Produk <span class="text-red-500">*</span></label><input type="text" name="name" required value="{{ old('name', $product->name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('description', $product->description ?? '') }}</textarea></div>
        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Harga <span class="text-red-500">*</span></label><input type="number" name="price" required min="0" step="0.01" value="{{ old('price', $product->price ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Stok</label><input type="number" name="stock" min="0" value="{{ old('stock', $product->stock ?? '0') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Satuan</label><input type="text" name="unit" value="{{ old('unit', $product->unit ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2" placeholder="pcs/kg"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label><input type="text" name="category" value="{{ old('category', $product->category ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="active" {{ old('status', $product->status ?? 'active') == 'active' ? 'selected' : '' }}>Aktif</option><option value="inactive" {{ old('status', $product->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Nonaktif</option></select></div>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($product) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
