@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Transaksi Tabungan</h1>
        <a href="{{ route('koperasi.savings') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">{{ $saving->user?->name ?? 'Unknown' }}</h2>
                <p class="text-sm text-slate-500">
                    Tipe: {{ $saving->type === 'mandatory' ? 'Wajib' : 'Sukarela' }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500">Saldo Saat Ini</p>
                <p class="text-2xl font-bold text-slate-900">Rp {{ number_format($saving->balance, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Transaksi Baru</h3>
        <form action="{{ route('koperasi.savings.transaction', $saving) }}" method="POST" class="flex flex-wrap items-end gap-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Tipe Transaksi</label>
                <select name="type" required class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="deposit">Setoran</option>
                    <option value="withdraw">Penarikan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Jumlah</label>
                <input type="number" name="amount" min="0" required class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 w-40">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-slate-600 mb-1">Keterangan</label>
                <input type="text" name="description" placeholder="Opsional" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Tanggal</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Tipe</th>
                        <th class="text-right px-4 py-3 font-medium text-slate-700">Jumlah</th>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-600 text-xs">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($t->type === 'deposit')
                            <span class="inline-block px-2 py-1 bg-emerald-100 text-emerald-800 rounded text-xs font-medium">Setoran</span>
                            @else
                            <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium">Penarikan</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $t->type === 'deposit' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $t->type === 'deposit' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $t->description ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-slate-500 py-12">Belum ada transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($transactions->hasPages())
    <div class="px-6 py-4">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection
