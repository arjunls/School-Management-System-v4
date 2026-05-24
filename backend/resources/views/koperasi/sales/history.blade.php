@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Riwayat Penjualan</h1>
        <a href="{{ route('koperasi.sales') }}" class="mt-4 sm:mt-0 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i>Transaksi Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('koperasi.sales.history') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Produk</label>
                <select name="product_id" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Produk</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="{{ route('koperasi.sales.history') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-colors text-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Waktu</th>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Produk</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Jumlah</th>
                        <th class="text-right px-4 py-3 font-medium text-slate-700">Total</th>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Pembeli</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-600 text-xs">{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-slate-900">{{ $sale->product?->name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ $sale->quantity }} {{ $sale->product?->unit ?? 'pcs' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-slate-900">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $sale->buyer?->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-slate-500 py-12">Belum ada penjualan.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($sales->count() > 0)
                <tfoot class="bg-slate-50 border-t border-slate-200">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-semibold text-slate-900">Grand Total</td>
                        <td class="px-4 py-3 text-right font-bold text-slate-900">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    @if($sales->hasPages())
    <div class="px-6 py-4">
        {{ $sales->links() }}
    </div>
    @endif
</div>
@endsection
