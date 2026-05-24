@extends('layouts.app')
@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('kalender.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">Tambah Event</h1></div>
    <form method="POST" action="{{ route('kalender.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Judul <span class="text-red-500">*</span></label><input type="text" name="title" required class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label><input type="date" name="start_date" required class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Selesai</label><input type="date" name="end_date" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jam Mulai</label><input type="time" name="start_time" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jam Selesai</label><input type="time" name="end_time" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Tipe</label><select name="type" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><option value="">Umum</option><option value="holiday">Libur</option><option value="exam">Ujian</option><option value="activity">Kegiatan</option></select></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Warna</label><input type="color" name="color" value="#3b82f6" class="w-12 h-10 rounded border border-slate-300"></div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Simpan</button>
    </form>
</div>
@endsection
