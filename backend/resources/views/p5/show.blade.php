@extends('layouts.app')
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('p5.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ $p5->title }}</h1></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="font-semibold text-slate-900 mb-4">Detail Projek</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-slate-500">Kelas:</span><p class="font-medium">{{ $p5->class->name }}</p></div>
                    <div><span class="text-slate-500">Status:</span><p class="font-medium"><span class="px-2 py-0.5 rounded-full text-xs {{ $p5->status === 'completed' ? 'bg-green-100 text-green-800' : ($p5->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-600') }}">{{ $p5->status }}</span></p></div>
                    @if($p5->theme)<div><span class="text-slate-500">Tema:</span><p>{{ $p5->theme }}</p></div>@endif
                    @if($p5->dimension)<div><span class="text-slate-500">Dimensi:</span><p>{{ $p5->dimension }}</p></div>@endif
                    <div><span class="text-slate-500">Periode:</span><p>{{ $p5->start_date ? $p5->start_date->format('d M Y') : '-' }} - {{ $p5->end_date ? $p5->end_date->format('d M Y') : '-' }}</p></div>
                </div>
                @if($p5->description)<p class="text-sm text-slate-600 mt-4">{{ $p5->description }}</p>@endif
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="font-semibold text-slate-900 mb-4">Kegiatan ({{ $p5->activities->count() }})</h2>
                <div class="divide-y divide-slate-100">
                    @forelse($p5->activities as $a)
                    <div class="py-3 flex items-start justify-between">
                        <div>
                            <p class="font-medium text-slate-900">{{ $a->name }}</p>
                            @if($a->description)<p class="text-sm text-slate-500">{{ $a->description }}</p>@endif
                            <div class="flex items-center gap-3 text-xs text-slate-400 mt-1">
                                @if($a->date)<span><i class="fas fa-calendar mr-1"></i>{{ $a->date->format('d M Y') }}</span>@endif
                                @if($a->location)<span><i class="fas fa-map-marker-alt mr-1"></i>{{ $a->location }}</span>@endif
                            </div>
                        </div>
                        <form action="{{ route('p5.activity.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 py-4">Belum ada kegiatan</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="font-semibold text-slate-900 mb-4">Tambah Kegiatan</h2>
                <form method="POST" action="{{ route('p5.activity.store', $p5) }}" class="space-y-3">
                    @csrf
                    <input type="text" name="name" placeholder="Nama Kegiatan" required class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                    <textarea name="description" placeholder="Deskripsi" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm"></textarea>
                    <input type="date" name="date" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                    <input type="text" name="location" placeholder="Lokasi" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm transition-colors">Tambah</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
