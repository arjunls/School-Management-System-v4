@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('printing.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">Cetak Buku Induk</h1></div>
    <form method="POST" action="{{ route('printing.buku-induk') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Filter Kelas (opsional)</label>
            <select name="class_id" class="w-full rounded-lg border border-slate-300 px-4 py-2">
                <option value="">Semua Kelas</option>
                @foreach($classes as $k)
                <option value="{{ $k->id }}">{{ $k->name }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Angkatan (opsional)</label><input type="text" name="angkatan" class="w-full rounded-lg border border-slate-300 px-4 py-2" placeholder="Contoh: 2025"></div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-file-pdf mr-2"></i>Cetak PDF</button>
    </form>
</div>
@endsection
