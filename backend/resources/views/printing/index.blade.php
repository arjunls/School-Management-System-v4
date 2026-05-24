@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-900">Cetak Massal</h1>

    <div class="grid gap-6 md:grid-cols-3">
        <a href="{{ route('printing.kartu-pelajar') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md hover:border-indigo-300 transition-all group">
            <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-indigo-200 transition-colors">
                <i class="fas fa-id-card text-2xl text-indigo-600"></i>
            </div>
            <h2 class="font-bold text-slate-900 mb-1">Kartu Pelajar</h2>
            <p class="text-sm text-slate-500">Cetak kartu pelajar per siswa</p>
        </a>

        <a href="{{ route('printing.kwitansi') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md hover:border-emerald-300 transition-all group">
            <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition-colors">
                <i class="fas fa-receipt text-2xl text-emerald-600"></i>
            </div>
            <h2 class="font-bold text-slate-900 mb-1">Kwitansi</h2>
            <p class="text-sm text-slate-500">Cetak kwitansi pembayaran</p>
        </a>

        <a href="{{ route('printing.legger') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md hover:border-amber-300 transition-all group">
            <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-200 transition-colors">
                <i class="fas fa-table text-2xl text-amber-600"></i>
            </div>
            <h2 class="font-bold text-slate-900 mb-1">Legger Nilai</h2>
            <p class="text-sm text-slate-500">Cetak daftar nilai per kelas & mapel</p>
        </a>

        <a href="{{ route('printing.buku-induk') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md hover:border-purple-300 transition-all group">
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-purple-200 transition-colors">
                <i class="fas fa-book text-2xl text-purple-600"></i>
            </div>
            <h2 class="font-bold text-slate-900 mb-1">Buku Induk</h2>
            <p class="text-sm text-slate-500">Cetak buku induk siswa</p>
        </a>

        <a href="{{ route('printing.ijazah') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md hover:border-blue-300 transition-all group">
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors">
                <i class="fas fa-graduation-cap text-2xl text-blue-600"></i>
            </div>
            <h2 class="font-bold text-slate-900 mb-1">Ijazah</h2>
            <p class="text-sm text-slate-500">Cetak ijazah per siswa</p>
        </a>

        <a href="{{ route('printing.skhu') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md hover:border-cyan-300 transition-all group">
            <div class="w-14 h-14 bg-cyan-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-cyan-200 transition-colors">
                <i class="fas fa-file-alt text-2xl text-cyan-600"></i>
            </div>
            <h2 class="font-bold text-slate-900 mb-1">SKHU</h2>
            <p class="text-sm text-slate-500">Surat Keterangan Hasil Ujian</p>
        </a>
    </div>
</div>
@endsection
