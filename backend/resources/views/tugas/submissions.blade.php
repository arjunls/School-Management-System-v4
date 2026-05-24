@extends('layouts.app')
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('tugas.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">Pengumpulan: {{ $tugas->title }}</h1></div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <div><span class="text-sm text-slate-500">{{ $tugas->class->name }} &middot; {{ $tugas->subject->name }} &middot; Tenggat: {{ $tugas->due_date->format('d M Y H:i') }}</span></div>
            <span class="text-sm font-medium">{{ $tugas->submissions->count() }} pengumpulan</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($tugas->submissions as $s)
            <div class="p-4 flex items-center justify-between hover:bg-slate-50">
                <div>
                    <p class="font-medium text-slate-900">{{ $s->student->name ?? '-' }}</p>
                    @if($s->file_path)<a href="#" class="text-xs text-blue-600 hover:underline"><i class="fas fa-download mr-1"></i>Download</a>@endif
                    @if($s->notes)<p class="text-xs text-slate-500 mt-1">{{ $s->notes }}</p>@endif
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm {{ $s->score !== null ? 'text-emerald-600 font-medium' : 'text-slate-400' }}">{{ $s->score !== null ? $s->score.'/'.$tugas->max_score : 'Belum dinilai' }}</span>
                    <button onclick="document.getElementById('grade-{{ $s->id }}').classList.toggle('hidden')" class="text-sm text-blue-600 hover:text-blue-800"><i class="fas fa-check-circle"></i> Nilai</button>
                </div>
            </div>
            <div id="grade-{{ $s->id }}" class="hidden bg-slate-50 px-4 py-3 border-b border-slate-100">
                <form method="POST" action="{{ route('tugas.grade', $s) }}" class="flex items-center gap-3 max-w-lg">
                    @csrf
                    <input type="number" name="score" placeholder="Nilai (0-{{ $tugas->max_score }})" min="0" max="{{ $tugas->max_score }}" value="{{ $s->score }}" required class="w-24 rounded-lg border border-slate-300 px-3 py-1.5 text-sm">
                    <input type="text" name="feedback" placeholder="Feedback" value="{{ $s->feedback }}" class="flex-1 rounded-lg border border-slate-300 px-3 py-1.5 text-sm">
                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm transition-colors">Simpan</button>
                </form>
            </div>
            @empty
            <div class="text-center text-slate-500 py-12">Belum ada pengumpulan</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
