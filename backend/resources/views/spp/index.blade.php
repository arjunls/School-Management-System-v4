@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">SPP Online</h1><a href="{{ route('spp.generate.form') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-file-invoice mr-2"></i>Generate Tagihan</a></div>

    @if(!isset($student))
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @php
            $paid = $stats->where('status', 'paid')->first();
            $unpaid = $stats->where('status', '!=', 'paid')->first();
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Belum Dibayar</p><p class="text-3xl font-bold text-rose-600">{{ $unpaidCount }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Total Outstanding</p><p class="text-3xl font-bold text-amber-600">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Lunas</p><p class="text-3xl font-bold text-emerald-600">{{ $paid ? $paid->count : 0 }}</p></div>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">{{ isset($student) ? 'Tagihan: ' . $student->name : 'Semua Tagihan' }}</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Siswa</th><th class="text-left px-4 py-3 font-medium">Jenis</th><th class="text-left px-4 py-3 font-medium">Jumlah</th><th class="text-left px-4 py-3 font-medium">Jatuh Tempo</th><th class="text-left px-4 py-3 font-medium">Status</th><th class="text-left px-4 py-3 font-medium">Sudah Dibayar</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $inv->student->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $inv->feeType->name ?? '-' }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $inv->due_date ? $inv->due_date->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                @if($inv->status == 'paid') bg-green-100 text-green-800
                                @elseif($inv->status == 'unpaid') bg-red-100 text-red-800
                                @else bg-amber-100 text-amber-800
                                @endif">{{ $inv->status }}</span>
                        </td>
                        <td class="px-4 py-3">Rp {{ number_format($inv->getPaidAmount(), 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-slate-500 py-12">Tidak ada tagihan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(!isset($student) && $invoices->hasPages())<div class="px-6 py-4">{{ $invoices->links() }}</div>@endif
    </div>
</div>
@endsection
