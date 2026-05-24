@extends('layouts.app')
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('kuis.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">Soal: {{ $kuis->title }}</h1></div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Tambah Soal</h2>
        <form method="POST" action="{{ route('kuis.question.store', $kuis) }}" class="space-y-4">
            @csrf
            <div><textarea name="question" placeholder="Pertanyaan" required rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2"></textarea></div>
            <div class="grid grid-cols-3 gap-4">
                <select name="type" required class="rounded-lg border border-slate-300 px-4 py-2"><option value="multiple_choice">Pilihan Ganda</option><option value="essay">Essay</option><option value="true_false">True/False</option></select>
                <input type="number" name="points" placeholder="Poin" value="10" min="1" required class="rounded-lg border border-slate-300 px-4 py-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Tambah Soal</button>
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Opsi (JSON untuk pilihan ganda)</label><textarea name="options" placeholder='["Opsi A","Opsi B","Opsi C","Opsi D"]' rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2 font-mono text-sm"></textarea></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jawaban Benar</label><input type="text" name="correct_answer" placeholder="A" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200"><h2 class="font-semibold text-slate-900">Daftar Soal ({{ $kuis->questions->count() }})</h2></div>
        <div class="divide-y divide-slate-100">
            @forelse($kuis->questions as $i => $q)
            <div class="p-4 hover:bg-slate-50">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <span class="text-xs text-slate-400 font-mono">#{{ $i+1 }}</span>
                        <p class="text-slate-900 mt-1">{{ $q->question_text }}</p>
                        <div class="flex items-center gap-3 mt-2 text-xs text-slate-500">
                            <span class="px-1.5 py-0.5 bg-slate-100 rounded">{{ $q->type }}</span>
                            <span>{{ $q->points }} poin</span>
                            @if($q->correct_answer)<span class="text-emerald-600">Jawaban: {{ $q->correct_answer }}</span>@endif
                        </div>
                    </div>
                    <form action="{{ route('kuis.question.destroy', $q) }}" method="POST" onsubmit="return confirm('Hapus soal?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                </div>
            </div>
            @empty
            <div class="text-center text-slate-500 py-12">Belum ada soal</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
