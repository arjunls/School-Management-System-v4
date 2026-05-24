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
        <h1 class="text-2xl font-bold text-slate-900">Manajemen Aset</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('asset.assets') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-box"></i>
                Daftar Aset
            </a>
            <a href="{{ route('asset.loans') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                <i class="fas fa-hand-holding"></i>
                Peminjaman
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Aset</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalAset) }}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg"><i class="fas fa-boxes text-blue-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Tersedia</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($tersedia) }}</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg"><i class="fas fa-check-circle text-green-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Dipinjam</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($dipinjam) }}</p>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg"><i class="fas fa-hand-holding text-orange-600 text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Perawatan</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($perawatan) }}</p>
                </div>
                <div class="bg-yellow-50 p-3 rounded-lg"><i class="fas fa-wrench text-yellow-600 text-xl"></i></div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-900">Peringatan Stok Habis Pakai</h2>
                    @if($lowStock->count() > 0)
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ $lowStock->count() }}</span>
                    @endif
                </div>
                <a href="{{ route('asset.consumables') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Kelola</a>
            </div>
            @if($lowStock->count() > 0)
            <div class="space-y-2">
                @foreach($lowStock as $c)
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <div>
                        <p class="font-medium text-slate-900">{{ $c->name }}</p>
                        <p class="text-sm text-slate-500">Stok: {{ $c->stock }} {{ $c->unit }} (Min: {{ $c->min_stock }})</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                        {{ $c->stock <= 0 ? 'Habis' : 'Menipis' }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-slate-400">Semua stok dalam batas aman</p>
            @endif
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-900">Peminjaman Terbaru</h2>
                <a href="{{ route('asset.loans') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
            </div>
            <div class="space-y-3">
                @forelse($recentLoans as $loan)
                <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-lg transition-colors">
                    <div>
                        <p class="font-medium text-slate-900">{{ $loan->asset->name ?? '-' }}</p>
                        <p class="text-sm text-slate-500">Dipinjam oleh {{ $loan->borrower->name ?? '-' }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $loan->status === 'borrowed' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                        {{ $loan->status === 'borrowed' ? 'Dipinjam' : 'Dikembalikan' }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-slate-400">Belum ada peminjaman</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-slate-900">Aset Terbaru</h2>
            <a href="{{ route('asset.assets') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($recentAssets as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $a->code ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $a->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $a->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $a->location ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $statusColors = ['available' => 'bg-green-100 text-green-800', 'borrowed' => 'bg-orange-100 text-orange-800', 'maintenance' => 'bg-yellow-100 text-yellow-800', 'retired' => 'bg-red-100 text-red-800']; @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$a->status] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada aset</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
