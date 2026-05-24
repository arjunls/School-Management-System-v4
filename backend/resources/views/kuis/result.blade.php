@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full
                {{ $attempt->score >= ($attempt->quiz->passing_score ?? 0) ? 'bg-emerald-100' : 'bg-red-100' }}
                mb-4">
                <i class="fas fa-{{ $attempt->score >= ($attempt->quiz->passing_score ?? 0) ? 'check-circle' : 'times-circle' }}
                    text-3xl {{ $attempt->score >= ($attempt->quiz->passing_score ?? 0) ? 'text-emerald-600' : 'text-red-600' }}"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Hasil Ujian</h1>
            <p class="text-slate-500 mt-1">{{ $attempt->quiz->title }}</p>
        </div>

        <div class="grid grid-cols-3 gap-4 max-w-lg mx-auto mb-6">
            <div class="text-center p-4 bg-slate-50 rounded-xl">
                <p class="text-3xl font-bold text-blue-600">{{ $attempt->score ?? 0 }}</p>
                <p class="text-xs text-slate-500 mt-1">Nilai</p>
            </div>
            <div class="text-center p-4 bg-slate-50 rounded-xl">
                <p class="text-3xl font-bold text-slate-700">{{ $totalPoints }}</p>
                <p class="text-xs text-slate-500 mt-1">Total Poin</p>
            </div>
            <div class="text-center p-4 bg-slate-50 rounded-xl">
                <p class="text-3xl font-bold text-{{ $attempt->score >= ($attempt->quiz->passing_score ?? 0) ? 'emerald' : 'red' }}-600">
                    {{ $attempt->score >= ($attempt->quiz->passing_score ?? 0) ? 'LULUS' : 'TIDAK LULUS' }}
                </p>
                <p class="text-xs text-slate-500 mt-1">{{ $attempt->quiz->passing_score ? 'Minimal ' . $attempt->quiz->passing_score : 'Status' }}</p>
            </div>
        </div>

        <div class="text-center text-xs text-slate-400">
            Dimulai: {{ $attempt->started_at ? $attempt->started_at->format('d/m/Y H:i') : '-' }} |
            Dikumpulkan: {{ $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i') : '-' }}
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Review Jawaban</h2>
        <div class="space-y-4">
            @foreach($attempt->quiz->questions as $index => $q)
            @php
                $answer = $answers->firstWhere('question_id', $q->id);
                $isCorrect = $q->type === 'multiple_choice' && $answer && $answer->answer_text === $q->correct_answer;
                $isCorrectTF = $q->type === 'true_false' && $answer && $answer->answer_text === $q->correct_answer;
            @endphp
            <div class="p-4 border rounded-xl {{ $isCorrect || $isCorrectTF ? 'border-emerald-200 bg-emerald-50' : ($answer ? 'border-red-200 bg-red-50' : 'border-slate-200') }}">
                <div class="flex items-start justify-between mb-2">
                    <p class="font-medium text-slate-900">
                        <span class="mr-2">{{ $index + 1 }}.</span>{{ $q->question_text }}
                    </p>
                    <span class="text-xs font-medium {{ $isCorrect || $isCorrectTF ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $q->points }} poin
                    </span>
                </div>
                <div class="text-sm space-y-1">
                    <p><span class="text-slate-500">Jawaban Anda:</span>
                        <span class="font-medium {{ $isCorrect || $isCorrectTF ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ $answer->answer_text ?? '(Tidak dijawab)' }}
                        </span>
                    </p>
                    @if($q->correct_answer && (!$isCorrect && !$isCorrectTF))
                    <p><span class="text-slate-500">Jawaban Benar:</span>
                        <span class="font-medium text-emerald-700">{{ $q->correct_answer }}</span>
                    </p>
                    @endif
                    @if($q->type === 'essay' && $answer && $answer->score !== null)
                    <p><span class="text-slate-500">Skor Guru:</span>
                        <span class="font-medium text-blue-700">{{ $answer->score }}</span>
                    </p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('kuis.index') }}" class="px-6 py-2.5 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Kuis
        </a>
    </div>
</div>
@endsection
