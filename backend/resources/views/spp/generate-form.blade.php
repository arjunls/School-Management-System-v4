@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('spp.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">Generate Tagihan SPP</h1></div>
    <form method="POST" action="{{ route('spp.generate') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Jenis Tagihan <span class="text-red-500">*</span></label>
            <select name="fee_type_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2">
                <option value="">Pilih Jenis</option>
                @foreach($feeTypes as $ft)
                <option value="{{ $ft->id }}" {{ old('fee_type_id') == $ft->id ? 'selected' : '' }}>{{ $ft->name }} (Rp {{ number_format($ft->amount, 0, ',', '.') }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Bulan</label>
                <select name="month" class="w-full rounded-lg border border-slate-300 px-4 py-2">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ (old('month') ?: now()->month) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tahun</label>
                <select name="year" class="w-full rounded-lg border border-slate-300 px-4 py-2">
                    @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                    <option value="{{ $y }}" {{ (old('year') ?: now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jatuh Tempo</label><input type="date" name="due_date" value="{{ old('due_date', now()->addMonth()->startOfMonth()->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jumlah (biarkan kosong untuk pakai default)</label><input type="number" name="amount" min="0" step="0.01" value="{{ old('amount') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <p class="text-sm text-slate-500">Tagihan akan dibuat untuk semua siswa aktif. Jika tagihan untuk bulan yang sama sudah ada, akan dilewati.</p>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-file-invoice mr-2"></i>Generate Tagihan</button>
    </form>
</div>
@endsection
