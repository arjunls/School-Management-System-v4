@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><div class="flex items-center gap-3"><a href="{{ route('tefa.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">Produk TEFA</h1></div><a href="{{ route('tefa.create.product') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-plus mr-2"></i>Tambah Produk</a></div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Nama</th><th class="text-left px-4 py-3 font-medium">Kategori</th><th class="text-left px-4 py-3 font-medium">Harga</th><th class="text-left px-4 py-3 font-medium">Stok</th><th class="text-left px-4 py-3 font-medium">Satuan</th><th class="text-left px-4 py-3 font-medium">Status</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                        <td class="px-4 py-3">{{ $p->category ?? '-' }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $p->stock <= 5 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ $p->stock }}</span></td>
                        <td class="px-4 py-3">{{ $p->unit ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $p->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $p->status }}</span></td>
                        <td class="px-4 py-3"><div class="flex gap-2"><a href="{{ route('tefa.edit.product', $p) }}" class="text-amber-600 hover:text-amber-800"><i class="fas fa-edit"></i></a><form action="{{ route('tefa.destroy.product', $p) }}" method="POST" onsubmit="return confirm('Hapus produk?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></div></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-slate-500 py-12">Belum ada produk</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())<div class="px-6 py-4">{{ $products->links() }}</div>@endif
    </div>
</div>
@endsection
