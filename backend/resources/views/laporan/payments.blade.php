@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan.index') }}" class="text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-2xl font-bold text-slate-900">Laporan Pembayaran</h1>
        </div>
        <a href="{{ route('pembayaran.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"><i class="fas fa-plus mr-2"></i>Kelola Pembayaran</a>
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $inv->student?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $inv->feeType?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Rp {{ number_format($inv->getPaidAmount(), 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Rp {{ number_format($inv->getRemainingAmount(), 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $sm = ['unpaid' => 'bg-yellow-100 text-yellow-800', 'paid' => 'bg-green-100 text-green-800', 'partial' => 'bg-blue-100 text-blue-800', 'overdue' => 'bg-red-100 text-red-800']; @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sm[$inv->status] ?? '' }}">{{ ucfirst($inv->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-slate-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())<div class="px-6 py-4 border-t border-slate-200">{{ $invoices->links() }}</div>@endif
    </div>
</div>
@endsection
