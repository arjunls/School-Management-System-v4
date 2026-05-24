@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Dashboard Koperasi</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('koperasi.products') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-box"></i> Produk
            </a>
            <a href="{{ route('koperasi.sales') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
                <i class="fas fa-cash-register"></i> POS
            </a>
            <a href="{{ route('koperasi.savings') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                <i class="fas fa-piggy-bank"></i> Tabungan
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Total Produk</p>
                    <p class="text-xl font-bold text-slate-900">{{ $totalProducts }}</p>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-2">{{ $activeProducts }} produk aktif</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Penjualan Hari Ini</p>
                    <p class="text-xl font-bold text-slate-900">Rp {{ number_format($todaySales, 0, ',', '.') }}</p>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-2">{{ $todaySalesCount }} transaksi</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Total Tabungan</p>
                    <p class="text-xl font-bold text-slate-900">Rp {{ number_format($totalSavingsBalance, 0, ',', '.') }}</p>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-2">{{ $totalMembers }} anggota</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Stok Menipis</p>
                    <p class="text-xl font-bold text-slate-900">{{ $lowStockProducts->count() }}</p>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-2">produk dengan stok &le; 5</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-slate-900">Penjualan Terbaru</h2>
                <a href="{{ route('koperasi.sales.history') }}" class="text-sm text-blue-600 hover:text-blue-800">Lihat Semua</a>
            </div>
            <div class="space-y-3">
                @forelse($recentSales as $sale)
                <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-slate-900">{{ $sale->product?->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500">{{ $sale->buyer?->name ?? '-' }} &middot; {{ $sale->quantity }} {{ $sale->product?->unit ?? 'pcs' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-slate-900">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-500">{{ $sale->sold_at->format('H:i') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-500 text-center py-6">Belum ada penjualan.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-slate-900">Stok Menipis</h2>
                <a href="{{ route('koperasi.products') }}" class="text-sm text-blue-600 hover:text-blue-800">Kelola Produk</a>
            </div>
            <div class="space-y-3">
                @forelse($lowStockProducts as $product)
                <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-slate-900">{{ $product->name }}</p>
                        <p class="text-xs text-slate-500">{{ $product->category }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium">
                            {{ $product->stock }} {{ $product->unit }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-500 text-center py-6">Semua stok aman.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
