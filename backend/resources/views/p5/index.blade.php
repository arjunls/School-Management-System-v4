@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Projek P5</h1><a href="{{ route('p5.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Buat Projek</a></div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($projects as $p)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start justify-between mb-3">
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $p->status === 'completed' ? 'bg-green-100 text-green-800' : ($p->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-600') }}">{{ $p->status }}</span>
                <div class="flex gap-2">
                    <a href="{{ route('p5.show', $p) }}" class="text-blue-600 hover:text-blue-800 text-sm"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('p5.edit', $p) }}" class="text-amber-600 hover:text-amber-800 text-sm"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('p5.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                </div>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">{{ $p->title }}</h3>
            <p class="text-sm text-slate-500 mb-3">{{ Str::limit($p->description, 80) }}</p>
            <div class="space-y-1 text-xs text-slate-400">
                <div><i class="fas fa-users mr-1 w-4"></i>Kelas: {{ $p->class->name ?? '-' }}</div>
                @if($p->theme)<div><i class="fas fa-palette mr-1 w-4"></i>Tema: {{ $p->theme }}</div>@endif
                @if($p->dimension)<div><i class="fas fa-ruler mr-1 w-4"></i>Dimensi: {{ $p->dimension }}</div>@endif
                <div><i class="fas fa-calendar mr-1 w-4"></i>{{ $p->start_date ? $p->start_date->format('d M Y') : '-' }} - {{ $p->end_date ? $p->end_date->format('d M Y') : '-' }}</div>
                <div><i class="fas fa-clipboard-list mr-1 w-4"></i>{{ $p->activities->count() }} kegiatan</div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-slate-500 py-12">Belum ada projek P5</div>
        @endforelse
    </div>
    @if($projects->hasPages())<div class="px-6 py-4">{{ $projects->links() }}</div>@endif
</div>
@endsection
