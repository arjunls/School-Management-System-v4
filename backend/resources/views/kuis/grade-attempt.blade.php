@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('kuis.grades', $attempt->quiz) }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">Nilai: {{ $attempt->student->name }}</h1>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $attempt->score ?? 0 }}</p>
            <p class="text-xs text-slate-500">Skor Saat Ini</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-slate-700">{{ $totalPoints }}</p>
            <p class="text-xs text-slate-500">Total Poin</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-{{ $attempt->score >= ($attempt->quiz->passing_score ?? 0) ? 'emerald' : 'red' }}-600">
                {{ $attempt->score >= ($attempt->quiz->passing_score ?? 0) ? 'LULUS' : 'TIDAK LULUS' }}
            </p>
            <p class="text-xs text-slate-500">Status</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Review Jawaban</h2>
        <div class="space-y-6">
            @foreach($attempt->quiz->questions as $index => $q)
            @php $answer = $answers->firstWhere('question_id', $q->id); @endphp
            <div class="p-4 border border-slate-200 rounded-xl">
                <div class="flex items-start justify-between mb-3">
                    <p class="font-medium text-slate-900">
                        <span class="text-blue-600 mr-2">{{ $index + 1 }}.</span>
                        {{ $q->question_text }}
                        <span class="ml-2 text-xs font-normal text-slate-400">({{ $q->points }} poin - {{ $q->type }})</span>
                    </p>
                </div>

                @if($q->type === 'multiple_choice' || $q->type === 'true_false')
                <div class="text-sm space-y-1 mb-2">
                    <p><span class="text-slate-500">Jawaban:</span>
                        <span class="font-medium {{ $answer && $answer->answer_text === $q->correct_answer ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ $answer->answer_text ?? '(Tidak dijawab)' }}
                        </span>
                    </p>
                    <p><span class="text-slate-500">Jawaban Benar:</span>
                        <span class="font-medium text-emerald-700">{{ $q->correct_answer }}</span>
                    </p>
                </div>
                <div class="text-xs {{ $answer && $answer->answer_text === $q->correct_answer ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $answer && $answer->answer_text === $q->correct_answer ? '+ ' . $q->points . ' poin (otomatis)' : '0 poin (salah)' }}
                </div>
                @else
                <div class="text-sm space-y-3">
                    <p><span class="text-slate-500">Jawaban:</span></p>
                    <div class="p-3 bg-slate-50 rounded-lg text-slate-700">
                        {{ $answer->answer_text ?? '(Tidak dijawab)' }}
                    </div>
                    <form method="POST" action="{{ route('kuis.gradeQuestion', [$attempt, $q]) }}" class="flex items-center gap-3">
                        @csrf
                        <label class="text-sm text-slate-500">Beri Skor (0-{{ $q->points }}):</label>
                        <input type="number" name="score" value="{{ $answer->score ?? 0 }}" min="0" max="{{ $q->points }}" required
                            class="w-20 px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs">
                            <i class="fas fa-save mr-1"></i>Simpan
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
