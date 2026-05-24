@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('common.jadwal') }}</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('export.jadwal') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-file-export"></i>
                {{ __('common.export') }}
            </a>
            <a href="{{ route('jadwal.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                {{ __('common.add_new') }}
            </a>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        @foreach($days as $day)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-3 bg-orange-50 rounded-t-2xl border-b border-slate-200">
                <h3 class="font-bold text-slate-900 text-center">{{ $day }}</h3>
            </div>
            <div class="p-3 space-y-2 min-h-[200px]">
                @forelse($jadwalGrouped[$day] as $j)
                <div class="p-2 bg-slate-50 rounded-lg border border-slate-100 text-xs">
                    <p class="font-semibold text-slate-900">{{ $j->subject?->name ?? '-' }}</p>
                    <p class="text-slate-500">{{ $j->teacher?->name ?? '-' }}</p>
                    <p class="text-slate-400">{{ substr($j->start_time, 0, 5) }} - {{ substr($j->end_time, 0, 5) }}</p>
                    <p class="text-slate-400">{{ $j->class?->name ?? '-' }} @if($j->room) | {{ $j->room }} @endif</p>
                    <div class="flex justify-end space-x-1 mt-1">
                        <a href="{{ route('jadwal.edit', $j) }}" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit text-[10px]"></i></a>
                        <form action="{{ route('jadwal.destroy', $j) }}" method="POST" class="inline" onsubmit="return confirm('{{ __("common.confirm_delete") }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800"><i class="fas fa-trash text-[10px]"></i></button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">Tidak ada jadwal</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
