@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center space-y-4">
        @if($status === 'success')
        <div class="text-5xl text-green-500"><i class="fas fa-check-circle"></i></div>
        <h1 class="text-2xl font-bold text-green-700">Absensi Berhasil!</h1>
        <p class="text-slate-600">{{ $student->name }} telah tercatat hadir</p>
        <p class="text-sm text-slate-400">{{ now()->format('d/m/Y H:i') }}</p>
        @elseif($status === 'already')
        <div class="text-5xl text-yellow-500"><i class="fas fa-info-circle"></i></div>
        <h1 class="text-2xl font-bold text-yellow-700">Sudah Diabsen</h1>
        <p class="text-slate-600">{{ $student->name }} sudah tercatat hari ini</p>
        <p class="text-sm text-slate-400">Status: {{ $record->status }} - {{ $record->date }}</p>
        @endif
        <div class="pt-4">
            <a href="{{ route('qr.scanner') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Scan Lagi</a>
            <a href="{{ route('kehadiran.index') }}" class="ml-2 px-6 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors">Ke Hadiran</a>
        </div>
    </div>
</div>
@endsection
