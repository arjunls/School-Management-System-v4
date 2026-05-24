@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('career.index') }}" class="text-slate-500 hover:text-slate-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-slate-900">Bimbingan Karir: {{ $student->name }}</h1>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-4">
                <i class="fas fa-brain text-purple-600 mr-2"></i> Minat Bakat (RIASEC)
            </h2>
            @forelse($interests as $i)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-100 text-purple-700 mr-2">
                        {{ $i->code }}
                    </span>
                    <span class="text-sm">{{ $i->label }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1">
                        <div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-600 rounded-full" style="width: {{ $i->score }}%"></div>
                        </div>
                        <span class="text-xs font-medium text-slate-700">{{ $i->score }}</span>
                    </div>
                    <form method="POST" action="{{ route('career.interest.delete', $i->id) }}" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700 text-xs" onclick="return confirm('Hapus?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-500 text-center py-4">Belum ada data minat bakat</p>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-4">
                <i class="fas fa-road text-amber-600 mr-2"></i> Rencana Karir
            </h2>
            @forelse($plans as $p)
            <div class="py-2 border-b border-slate-100 last:border-0">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                            {{ $p->plan_type === 'study' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $p->plan_type === 'work' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $p->plan_type === 'entrepreneur' ? 'bg-amber-100 text-amber-700' : '' }}">
                            {{ $p->plan_type === 'study' ? 'Studi Lanjut' : ($p->plan_type === 'work' ? 'Bekerja' : 'Wirausaha') }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('career.plan.delete', $p->id) }}" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700 text-xs" onclick="return confirm('Hapus?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                @if($p->institution)
                <p class="text-sm text-slate-700 mt-1">{{ $p->institution }} @if($p->major) - {{ $p->major }} @endif</p>
                @endif
                @if($p->goal)
                <p class="text-xs text-slate-500 mt-1">Tujuan: {{ $p->goal }}</p>
                @endif
                @if($p->notes)
                <p class="text-xs text-slate-400 mt-1">{{ $p->notes }}</p>
                @endif
            </div>
            @empty
            <p class="text-sm text-slate-500 text-center py-4">Belum ada rencana karir</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="font-semibold text-slate-900 mb-3">Analisis Minat Bakat</h3>
        @php
            $topCode = $interests->sortByDesc('score')->first();
        @endphp
        @if($topCode)
        <div class="p-4 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-200">
            <p class="text-sm text-slate-700">
                <strong>Dominasi:</strong> Tipe <span class="font-bold text-purple-700">{{ $topCode->code }} ({{ $topCode->label }})</span>
                dengan skor {{ $topCode->score }}
            </p>
            <div class="mt-2 grid gap-2 md:grid-cols-3">
                @foreach($interests->sortByDesc('score') as $i)
                <div class="text-xs text-slate-600">
                    <span class="font-medium">{{ $i->code }}</span>:
                    <div class="w-full h-1.5 bg-slate-200 rounded-full mt-1">
                        <div class="h-full rounded-full
                            {{ $loop->first ? 'bg-purple-600' : '' }}
                            {{ $loop->index === 1 ? 'bg-indigo-500' : '' }}
                            {{ $loop->index === 2 ? 'bg-blue-500' : '' }}
                            {{ $loop->index >= 3 ? 'bg-slate-400' : '' }}"
                            style="width: {{ $i->score }}%">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <p class="text-sm text-slate-500">Belum ada data untuk analisis</p>
        @endif
    </div>
</div>
@endsection
