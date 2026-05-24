@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('tefa.sales') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">Tambah Penjualan</h1></div>
    <form method="POST" action="{{ route('tefa.store.sale') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Produk <span class="text-red-500">*</span></label>
            <select name="product_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2">
                <option value="">Pilih Produk</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} (stok: {{ $p->stock }} - Rp {{ number_format($p->price, 0, ',', '.') }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jumlah <span class="text-red-500">*</span></label><input type="number" name="quantity" required min="1" value="{{ old('quantity', 1) }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Total Harga <span class="text-red-500">*</span></label><input type="number" name="total_price" required min="0" step="0.01" value="{{ old('total_price') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Pelanggan</label><input type="text" name="customer_name" value="{{ old('customer_name') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Penjualan</label><input type="date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">Simpan Penjualan</button>
    </form>
</div>
@endsection
