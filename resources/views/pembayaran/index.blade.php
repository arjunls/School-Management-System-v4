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
        <h1 class="text-2xl font-bold text-slate-900">Pembayaran</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('pembayaran.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Buat Tagihan Baru
            </a>
            <a href="{{ route('export.siswa') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-file-export"></i>
                Ekspor
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tagihan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Telah Dibayar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Sisa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            <div class="font-medium">{{ $invoice->student?->name ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $invoice->student_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $invoice->feeType?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Rp {{ number_format($invoice->getPaidAmount(), 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Rp {{ number_format($invoice->getRemainingAmount(), 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusMap = ['unpaid' => ['bg-yellow-100 text-yellow-800', 'Belum Dibayar'], 'paid' => ['bg-green-100 text-green-800', 'Lunas'], 'partial' => ['bg-blue-100 text-blue-800', 'Angsuran'], 'overdue' => ['bg-red-100 text-red-800', 'Terlambat']];
                                [$statusClass, $statusLabel] = $statusMap[$invoice->status] ?? ['bg-slate-100 text-slate-800', $invoice->status];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            @if($invoice->status !== 'paid')
                            <form action="{{ route('pembayaran.pay', $invoice) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $invoice->getRemainingAmount() }}">
                                <input type="hidden" name="payment_date" value="{{ date('Y-m-d') }}">
                                <input type="hidden" name="payment_method" value="cash">
                                <button type="submit" class="text-emerald-600 hover:text-emerald-900 px-2 py-1 rounded hover:bg-emerald-50" title="Bayar Tunai">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>
                            </form>
                            <a href="{{ route('pembayaran.pay-online', $invoice) }}" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50" title="Bayar Online">
                                <i class="fas fa-globe"></i>
                            </a>
                            @endif
                            <form action="{{ route('pembayaran.destroy', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tagihan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-500">Belum ada tagihan pembayaran</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
