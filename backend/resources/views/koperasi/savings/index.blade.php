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
        <h1 class="text-2xl font-bold text-slate-900">Tabungan Anggota</h1>
        <div class="flex items-center gap-3 mt-4 sm:mt-0">
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-2 text-sm">
                <span class="text-slate-500">Total Saldo: </span>
                <span class="font-bold text-slate-900">Rp {{ number_format($totalBalance, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Buka Tabungan Baru</h2>
        <form action="{{ route('koperasi.savings.create') }}" method="POST" class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-slate-600 mb-1">Anggota</label>
                <select name="user_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Pilih Anggota</option>
                    @foreach(\App\Models\User::orderBy('name')->get() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Tipe</label>
                <select name="type" required class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="voluntary">Sukarela</option>
                    <option value="mandatory">Wajib</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Saldo Awal</label>
                <input type="number" name="initial_balance" value="0" min="0" required class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 w-32">
            </div>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm">
                <i class="fas fa-plus mr-1"></i> Buka Tabungan
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Anggota</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Tipe</th>
                        <th class="text-right px-4 py-3 font-medium text-slate-700">Saldo</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($savings as $saving)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-sm font-medium">
                                    {{ substr($saving->user?->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">{{ $saving->user?->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-500">{{ $saving->user?->role ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($saving->type === 'mandatory')
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">Wajib</span>
                            @else
                            <span class="inline-block px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-medium">Sukarela</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-slate-900">Rp {{ number_format($saving->balance, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('koperasi.savings.transactions', $saving) }}" class="text-sm text-purple-600 hover:text-purple-800">
                                <i class="fas fa-wallet mr-1"></i> Transaksi
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-slate-500 py-12">Belum ada tabungan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($savings->hasPages())
    <div class="px-6 py-4">
        {{ $savings->links() }}
    </div>
    @endif
</div>
@endsection
