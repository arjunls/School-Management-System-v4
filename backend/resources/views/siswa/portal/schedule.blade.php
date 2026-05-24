@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('siswa.portal.dashboard') }}" class="text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-2xl font-bold text-slate-900">Jadwal Saya</h1>
        </div>
        <span class="text-sm text-slate-500">{{ $user->kelas?->name ?? '-' }}</span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        @foreach($days as $key => $label)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-3 bg-blue-50 rounded-t-2xl border-b border-slate-200">
                <h3 class="font-bold text-slate-900 text-center text-sm">{{ $label }}</h3>
            </div>
            <div class="p-3 space-y-2 min-h-[150px]">
                @forelse(($schedules[$key] ?? []) as $j)
                <div class="p-2 bg-slate-50 rounded-lg border border-slate-100 text-xs">
                    <p class="font-semibold text-slate-900">{{ $j->subject?->name ?? '-' }}</p>
                    <p class="text-slate-500">{{ $j->teacher?->name ?? '-' }}</p>
                    <p class="text-slate-400">{{ substr($j->start_time, 0, 5) }} - {{ substr($j->end_time, 0, 5) }}</p>
                    @if($j->room)<p class="text-slate-400">{{ $j->room }}</p>@endif
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">-</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
