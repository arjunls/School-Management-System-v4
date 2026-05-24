@extends('layouts.app')

@section('content')
<div class="flex h-[calc(100vh-8rem)] gap-4">
    <div class="w-80 flex-shrink-0 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-bold text-slate-900">Pesan</h2>
            <a href="{{ route('messages.create') }}" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700"><i class="fas fa-plus mr-1"></i>Baru</a>
        </div>
        <div class="flex-1 overflow-y-auto">
            @forelse($conversations as $c)
            <a href="{{ route('messages.show', $c) }}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100 {{ request()->route('message')?->id === $c->id ? 'bg-blue-50' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-slate-200 rounded-full flex items-center justify-center text-sm font-semibold text-slate-600 flex-shrink-0">
                        {{ strtoupper(substr($c->subject ?? 'Tanpa Judul', 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-900 truncate">{{ $c->subject ?? 'Tanpa Judul' }}</p>
                        <p class="text-xs text-slate-500 truncate">
                            @foreach($c->participants as $p)
                                @if($p->id !== auth()->id()){{ $p->name }}@if(!$loop->last), @endif @endif
                            @endforeach
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $c->lastMessage?->created_at?->diffForHumans() ?? '' }}</p>
                    </div>
                </div>
            </a>
            @empty
            <p class="text-sm text-slate-500 text-center py-12">Belum ada percakapan</p>
            @endforelse
        </div>
    </div>
    <div class="flex-1 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-center">
        <div class="text-center">
            <i class="fas fa-comments text-5xl text-slate-300 mb-4"></i>
            <p class="text-slate-500">Pilih percakapan untuk memulai</p>
        </div>
    </div>
</div>
@endsection
