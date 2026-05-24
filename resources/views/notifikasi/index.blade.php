@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Notifikasi</h1>
        @if(auth()->user()->unreadNotifications->count() > 0)
        <form action="{{ route('notifikasi.markAllRead') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors text-sm">Tandai Semua Dibaca</button>
        </form>
        @endif
    </div>

    <div class="space-y-2">
        @forelse($notifications as $n)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 {{ $n->read_at ? '' : 'border-l-4 border-l-blue-500 bg-blue-50/50' }}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="font-medium text-slate-900">{{ $n->data['title'] ?? $n->type }}</p>
                    <p class="text-sm text-slate-600">{{ $n->data['body'] ?? 'Tidak ada konten' }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @if(!$n->read_at)
                <form action="{{ route('notifikasi.markRead', $n->id) }}" method="POST" class="ml-3">
                    @csrf
                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">Tandai Dibaca</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
            <p class="text-slate-500">Tidak ada notifikasi</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="px-6 py-4">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
