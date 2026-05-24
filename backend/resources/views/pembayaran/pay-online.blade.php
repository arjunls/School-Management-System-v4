@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Pembayaran Online</h1>
        <a href="{{ route('pembayaran.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center space-y-4">
        <div class="text-4xl text-slate-300"><i class="fas fa-credit-card"></i></div>
        <h2 class="text-xl font-semibold">{{ $invoice->student?->name }}</h2>
        <p class="text-slate-500">{{ $invoice->feeType?->name }}</p>
        <p class="text-3xl font-bold text-slate-900">Rp {{ number_format($invoice->getRemainingAmount(), 0, ',', '.') }}</p>
        <button id="pay-button" class="w-full px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-semibold">
            Bayar Sekarang
        </button>
    </div>
</div>
@endsection

@push('scripts')
@if($snapToken)
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('pay-button').addEventListener('click', function () {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function () { window.location.href = '{{ route("pembayaran.index") }}?success=1'; },
            onPending: function () { alert('Pembayaran sedang diproses'); },
            onError: function () { alert('Pembayaran gagal'); },
            onClose: function () { alert('Pembayaran dibatalkan'); }
        });
    });
</script>
@else
<script>
    document.getElementById('pay-button').addEventListener('click', function () {
        alert('Gateway pembayaran belum dikonfigurasi. Hubungi administrator.');
    });
</script>
@endif
@endpush
