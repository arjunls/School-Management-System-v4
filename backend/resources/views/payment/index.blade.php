@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Payment Gateway</h1>
        <a href="{{ route('payment.config') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-cog mr-2"></i>Konfigurasi</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Total Terkumpul</p>
            <p class="text-3xl font-bold text-emerald-600">Rp {{ number_format($totalCollected, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Menunggu Pembayaran</p>
            <p class="text-3xl font-bold text-amber-600">{{ $pendingCount }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Transaksi Total</p>
            <p class="text-3xl font-bold text-blue-600">{{ $transactions->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Riwayat Transaksi</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium">ID</th>
                        <th class="text-left px-4 py-3 font-medium">Siswa</th>
                        <th class="text-left px-4 py-3 font-medium">Provider</th>
                        <th class="text-left px-4 py-3 font-medium">Jumlah</th>
                        <th class="text-left px-4 py-3 font-medium">Status</th>
                        <th class="text-left px-4 py-3 font-medium">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $trx->transaction_id ?? 'TRX-' . $trx->id }}</td>
                        <td class="px-4 py-3 font-medium">{{ $trx->student->name ?? '-' }}</td>
                        <td class="px-4 py-3 capitalize">{{ $trx->provider }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                @if($trx->status == 'success') bg-green-100 text-green-800
                                @elseif($trx->status == 'pending') bg-amber-100 text-amber-800
                                @elseif($trx->status == 'failed') bg-red-100 text-red-800
                                @else bg-slate-100 text-slate-600
                                @endif">{{ $trx->status }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $trx->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-slate-500 py-12">Belum ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
