@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Kalender Akademik</h1>
        <a href="{{ route('kalender.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Tambah Event</a>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($events as $e)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start justify-between mb-3">
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $e->type === 'holiday' ? 'bg-red-100 text-red-800' : ($e->type === 'exam' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">{{ $e->type ?? 'event' }}</span>
                <form action="{{ route('kalender.destroy', $e) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">{{ $e->title }}</h3>
            <p class="text-sm text-slate-600 mb-3">{{ Str::limit($e->description, 120) }}</p>
            <div class="flex items-center gap-2 text-xs text-slate-400"><i class="fas fa-calendar-day"></i>{{ \Carbon\Carbon::parse($e->start_date)->format('d M Y') }}@if($e->end_date) - {{ \Carbon\Carbon::parse($e->end_date)->format('d M Y') }}@endif</div>
        </div>
        @empty
        <div class="col-span-full text-center text-slate-500 py-12">Belum ada event kalender</div>
        @endforelse
    </div>
    @if($events->hasPages())<div class="px-6 py-4">{{ $events->links() }}</div>@endif
</div>
@endsection
