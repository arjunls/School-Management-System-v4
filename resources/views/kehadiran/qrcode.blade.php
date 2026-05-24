@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">QR Code Absensi</h1>
        <a href="{{ route('siswa.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center space-y-4">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto">
            <i class="fas fa-user-graduate text-2xl text-blue-600"></i>
        </div>
        <h2 class="text-xl font-bold">{{ $student->name }}</h2>
        <p class="text-slate-500">NIS: {{ $student->id }}</p>
        <div class="flex justify-center p-4 bg-white rounded-xl">
            {!! $qrSvg !!}
        </div>
        <p class="text-xs text-slate-400">Scan QR ini untuk absensi harian</p>
        <a href="{{ route('qr.scanner') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
            <i class="fas fa-camera mr-2"></i>Buka Scanner
        </a>
    </div>
</div>
@endsection
