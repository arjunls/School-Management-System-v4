@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $quiz->title }}</h1>
                <p class="text-sm text-slate-500 mt-1">{{ $quiz->subject?->name }} - {{ $quiz->class?->name }}</p>
            </div>
            <div class="text-right">
                @if($quiz->time_limit)
                <div id="timer" class="text-2xl font-bold text-blue-600 font-mono" data-minutes="{{ $quiz->time_limit }}" data-started="{{ $attempt->started_at->timestamp }}">
                    {{ $quiz->time_limit }}:00
                </div>
                <p class="text-xs text-slate-500">sisa waktu</p>
                @endif
            </div>
        </div>

        <form id="quiz-form" method="POST" action="{{ route('kuis.submit', $attempt) }}">
            @csrf
            <div class="space-y-6">
                @foreach($quiz->questions as $index => $q)
                <div class="p-4 border border-slate-200 rounded-xl">
                    <div class="flex items-start justify-between mb-3">
                        <p class="font-medium text-slate-900">
                            <span class="text-blue-600 mr-2">{{ $index + 1 }}.</span>
                            {{ $q->question_text }}
                        </p>
                        <span class="text-xs text-slate-400">{{ $q->points }} poin</span>
                    </div>

                    @if($q->type === 'multiple_choice')
                    <div class="space-y-2 ml-6">
                        @php $options = is_string($q->options) ? json_decode($q->options, true) : $q->options; @endphp
                        @if(is_array($options))
                            @foreach($options as $key => $value)
                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ is_numeric($key) ? $value : $key }}" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-slate-700">{{ is_numeric($key) ? chr(65 + $key) . '. ' . $value : $key . '. ' . $value }}</span>
                            </label>
                            @endforeach
                        @endif
                    </div>
                    @elseif($q->type === 'true_false')
                    <div class="space-y-2 ml-6">
                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="answers[{{ $q->id }}]" value="true" class="text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-700">Benar</span>
                        </label>
                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="answers[{{ $q->id }}]" value="false" class="text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-700">Salah</span>
                        </label>
                    </div>
                    @else
                    <div class="ml-6">
                        <textarea name="answers[{{ $q->id }}]" rows="3" placeholder="Tulis jawaban Anda..." class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="mt-6 flex items-center justify-between">
                <p class="text-xs text-slate-400">Total Soal: {{ $quiz->questions->count() }}</p>
                <button type="submit" id="submit-btn" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fas fa-paper-plane mr-2"></i>Kumpulkan Jawaban
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@if($quiz->time_limit)
<script>
(function() {
    const timerEl = document.getElementById('timer');
    const startedAt = parseInt(timerEl.dataset.started);
    const totalMinutes = parseInt(timerEl.dataset.minutes);
    const totalSeconds = totalMinutes * 60;

    function updateTimer() {
        const now = Math.floor(Date.now() / 1000);
        const elapsed = now - startedAt;
        const remaining = Math.max(0, totalSeconds - elapsed);
        const mins = Math.floor(remaining / 60);
        const secs = remaining % 60;
        timerEl.textContent = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
        if (remaining <= 300 && remaining > 0) {
            timerEl.classList.add('text-red-500');
            timerEl.classList.remove('text-blue-600');
        }
        if (remaining <= 0) {
            timerEl.textContent = '00:00';
            timerEl.classList.add('text-red-600');
            document.getElementById('quiz-form').submit();
        }
    }
    updateTimer();
    setInterval(updateTimer, 1000);
})();
</script>
@endif
@endpush
