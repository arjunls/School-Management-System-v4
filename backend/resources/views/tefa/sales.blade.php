@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>@endif
    <div class="flex items-center justify-between"><div class="flex items-center gap-3"><a href="{{ route('tefa.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">Penjualan TEFA</h1></div><a href="{{ route('tefa.create.sale') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700"><i class="fas fa-plus mr-2"></i>Tambah Penjualan</a></div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Produk</th><th class="text-left px-4 py-3 font-medium">Jumlah</th><th class="text-left px-4 py-3 font-medium">Total</th><th class="text-left px-4 py-3 font-medium">Pelanggan</th><th class="text-left px-4 py-3 font-medium">Tanggal</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $s->product->name }}</td>
                        <td class="px-4 py-3">{{ $s->quantity }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($s->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $s->customer_name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-3"><form action="{{ route('tefa.destroy.sale', $s) }}" method="POST" onsubmit="return confirm('Hapus penjualan?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-slate-500 py-12">Belum ada penjualan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())<div class="px-6 py-4">{{ $sales->links() }}</div>@endif
    </div>
</div>
@endsection
