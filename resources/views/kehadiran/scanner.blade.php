@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Scan QR Code Absensi</h1>
        <a href="{{ route('kehadiran.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <p class="text-sm text-slate-600 mb-4">Arahkan kamera ke QR Code siswa untuk melakukan absensi</p>
        <div id="reader" class="w-full" style="min-height: 300px;"></div>
    </div>

    <div id="result" class="hidden">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center space-y-3">
            <div id="result-icon" class="text-4xl"></div>
            <p id="result-message" class="text-lg font-semibold"></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        function(qrMessage) {
            html5QrCode.stop();
            fetch(qrMessage, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(d => {
                    document.getElementById('result').classList.remove('hidden');
                    const icon = document.getElementById('result-icon');
                    const msg = document.getElementById('result-message');
                    if (d.success) {
                        icon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                        msg.textContent = d.message || 'Absensi berhasil';
                        msg.className = 'text-lg font-semibold text-green-700';
                    } else {
                        icon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
                        msg.textContent = d.message || 'Gagal';
                        msg.className = 'text-lg font-semibold text-red-700';
                    }
                    setTimeout(() => { window.location.href = '{{ route("kehadiran.index") }}'; }, 2000);
                })
                .catch(() => {
                    document.getElementById('result').classList.remove('hidden');
                    document.getElementById('result-icon').innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                    document.getElementById('result-message').textContent = 'Absensi berhasil';
                    document.getElementById('result-message').className = 'text-lg font-semibold text-green-700';
                    setTimeout(() => { window.location.href = '{{ route("kehadiran.index") }}'; }, 2000);
                });
        },
        function() {}
    );
</script>
@endpush
