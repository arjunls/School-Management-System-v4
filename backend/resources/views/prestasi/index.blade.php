@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Prestasi Siswa</h1></div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Tambah Prestasi</h2>
        <form method="POST" action="{{ route('prestasi.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <select name="student_id" required class="rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih Siswa</option>@foreach(\App\Models\User::where('role','student')->get() as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select>
            <input type="text" name="title" placeholder="Judul Prestasi" required class="rounded-lg border border-slate-300 px-4 py-2">
            <select name="type" class="rounded-lg border border-slate-300 px-4 py-2"><option value="">Jenis</option><option value="academic">Akademik</option><option value="sport">Olahraga</option><option value="art">Seni</option><option value="religious">Keagamaan</option><option value="other">Lainnya</option></select>
            <select name="level" class="rounded-lg border border-slate-300 px-4 py-2"><option value="">Tingkat</option><option value="school">Sekolah</option><option value="district">Kecamatan</option><option value="province">Provinsi</option><option value="national">Nasional</option><option value="international">Internasional</option></select>
            <input type="text" name="rank" placeholder="Juara / Peringkat" class="rounded-lg border border-slate-300 px-4 py-2">
            <input type="date" name="achievement_date" required class="rounded-lg border border-slate-300 px-4 py-2">
            <div class="flex items-end"><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Simpan</button></div>
        </form>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($achievements as $a)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ $a->type ?? '-' }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $a->level === 'international' ? 'bg-purple-100 text-purple-800' : ($a->level === 'national' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800') }}">{{ $a->level ?? '-' }}</span>
                </div>
                <form action="{{ route('prestasi.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trophy"></i></button></form>
            </div>
            <h3 class="font-bold text-slate-900 mb-1">{{ $a->title }}</h3>
            <p class="text-sm text-slate-600 mb-2">{{ $a->student->name }}</p>
            @if($a->rank)<p class="text-sm font-semibold text-amber-600">{{ $a->rank }}</p>@endif
            <p class="text-xs text-slate-400 mt-2">{{ \Carbon\Carbon::parse($a->achievement_date)->format('d M Y') }}</p>
        </div>
        @empty
        <div class="col-span-full text-center text-slate-500 py-12">Belum ada prestasi</div>
        @endforelse
    </div>
    @if($achievements->hasPages())<div class="px-6 py-4">{{ $achievements->links() }}</div>@endif
</div>
@endsection
