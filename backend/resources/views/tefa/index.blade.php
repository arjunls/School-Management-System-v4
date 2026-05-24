@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Teaching Factory (TEFA)</h1><div class="flex gap-2"><a href="{{ route('tefa.products') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-box mr-2"></i>Produk</a><a href="{{ route('tefa.productions') }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700"><i class="fas fa-industry mr-2"></i>Produksi</a><a href="{{ route('tefa.sales') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700"><i class="fas fa-shopping-cart mr-2"></i>Penjualan</a></div></div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Total Produk</p><p class="text-3xl font-bold text-slate-900">{{ $productCount }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Total Penjualan</p><p class="text-3xl font-bold text-emerald-600">Rp {{ number_format($totalSales, 0, ',', '.') }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Total Produksi</p><p class="text-3xl font-bold text-slate-900">{{ $totalProduction }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Stok Menipis</p><p class="text-3xl font-bold text-rose-600">{{ $lowStock }}</p></div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Penjualan Terbaru</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Produk</th><th class="text-left px-4 py-3 font-medium">Jumlah</th><th class="text-left px-4 py-3 font-medium">Total</th><th class="text-left px-4 py-3 font-medium">Pelanggan</th><th class="text-left px-4 py-3 font-medium">Tanggal</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentSales as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $s->product->name }}</td>
                        <td class="px-4 py-3">{{ $s->quantity }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($s->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $s->customer_name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-slate-500 py-8">Belum ada penjualan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
