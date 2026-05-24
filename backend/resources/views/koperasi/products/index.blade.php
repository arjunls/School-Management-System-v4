@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Produk Koperasi</h1>
        <a href="{{ route('koperasi.products.create') }}" class="mt-4 sm:mt-0 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i>Tambah Produk
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Produk</th>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Kategori</th>
                        <th class="text-right px-4 py-3 font-medium text-slate-700">Harga</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Stok</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Terjual</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Status</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                                    <i class="fas fa-box"></i>
                                </div>
                                @endif
                                <div>
                                    <p class="font-medium text-slate-900">{{ $product->name }}</p>
                                    @if($product->description)
                                    <p class="text-xs text-slate-500 truncate max-w-[200px]">{{ $product->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $product->category }}</td>
                        <td class="px-4 py-3 text-right font-medium text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-1 rounded text-xs font-medium {{ $product->stock > 5 ? 'bg-emerald-100 text-emerald-800' : ($product->stock > 0 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                {{ $product->stock }} {{ $product->unit }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ $product->sales_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($product->status === 'active')
                            <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-medium">Aktif</span>
                            @else
                            <span class="inline-block px-3 py-1 bg-slate-100 text-slate-800 rounded-full text-xs font-medium">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('koperasi.products.edit', $product) }}" class="text-sm text-green-600 hover:text-green-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('koperasi.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-slate-500 py-12">Belum ada produk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($products->hasPages())
    <div class="px-6 py-4">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
