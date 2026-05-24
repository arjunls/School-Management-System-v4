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
            <a href="{{ route('messages.show', $c) }}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100 {{ $message->id === $c->id ? 'bg-blue-50' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-slate-200 rounded-full flex items-center justify-center text-sm font-semibold text-slate-600 flex-shrink-0">
                        {{ strtoupper(substr($c->subject ?? 'TN', 0, 2)) }}
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
    <div class="flex-1 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col">
        <div class="p-4 border-b border-slate-200">
            <h2 class="font-bold text-slate-900">{{ $message->subject ?? 'Percakapan' }}</h2>
            <p class="text-xs text-slate-500">
                @foreach($message->participants as $p)
                    @if($p->id !== auth()->id()){{ $p->name }}@if(!$loop->last), @endif @endif
                @endforeach
            </p>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @forelse($messages as $msg)
            <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[70%] {{ $msg->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-900' }} rounded-2xl px-4 py-2.5">
                    <p class="text-sm">{{ $msg->body }}</p>
                    <p class="text-xs {{ $msg->sender_id === auth()->id() ? 'text-blue-200' : 'text-slate-400' }} mt-1">{{ $msg->created_at->format('H:i') }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-500 text-center py-12">Belum ada pesan</p>
            @endforelse
        </div>
        <div class="p-4 border-t border-slate-200">
            <form action="{{ route('messages.reply', $message) }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="body" required placeholder="Ketik pesan..." class="flex-1 px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>
@endsection
