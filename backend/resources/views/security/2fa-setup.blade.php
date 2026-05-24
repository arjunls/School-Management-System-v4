@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-900">Aktifkan 2FA</h1>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-6">
            <i class="fas fa-qrcode text-blue-600 mr-2"></i>Kode Rahasia
        </h2>

        <pre class="bg-slate-50 p-4 rounded-lg text-sm">
            {{ $secret }}
        </pre>

        <p class="text-sm">Bukti: Skasikan dengan aplikasi 2FA Anda (misal Google Authenticator)</p>

        <h3 class="mt-6 font-semibold">Kode QR Setup</h3>
        <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={{ $qrCodeUrl }}" alt="QR Code" class="mx-auto block max-w-sm border border-slate-300 rounded-xl shadow-sm">
    </div>
</div>
@endsection
