@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Kuis Online</h1><a href="{{ route('kuis.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Buat Kuis</a></div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($quizzes as $q)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start justify-between mb-3">
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $q->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">{{ $q->status === 'published' ? 'Terbit' : 'Draft' }}</span>
                <div class="flex gap-2">
                    @php $user = auth()->user(); @endphp
                    @if($q->status === 'published' && ($user->role === 'student' || $user->role === 'siswa'))
                        <a href="{{ route('kuis.take', $q) }}" class="text-emerald-600 hover:text-emerald-800 text-sm" title="Kerjakan"><i class="fas fa-play"></i></a>
                    @endif
                    @if($user->role !== 'student' && $user->role !== 'siswa')
                        <a href="{{ route('kuis.grades', $q) }}" class="text-indigo-600 hover:text-indigo-800 text-sm" title="Nilai"><i class="fas fa-check-double"></i></a>
                        <a href="{{ route('kuis.questions', $q) }}" class="text-blue-600 hover:text-blue-800 text-sm" title="Soal"><i class="fas fa-list"></i></a>
                        <a href="{{ route('kuis.edit', $q) }}" class="text-amber-600 hover:text-amber-800 text-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('kuis.destroy', $q) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                    @endif
                </div>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">{{ $q->title }}</h3>
            <p class="text-sm text-slate-500 mb-3">{{ $q->class->name ?? '-' }} &middot; {{ $q->subject->name ?? '-' }}</p>
            <div class="space-y-1 text-xs text-slate-400">
                <div><i class="fas fa-clock mr-1 w-4"></i>Batas waktu: {{ $q->time_limit ? $q->time_limit.' menit' : 'Tidak ada' }}</div>
                <div><i class="fas fa-check-circle mr-1 w-4"></i>Nilai lulus: {{ $q->passing_score }}</div>
                @if($q->due_date)<div><i class="fas fa-hourglass-end mr-1 w-4"></i>Tenggat: {{ $q->due_date->format('d M Y H:i') }}</div>@endif
                <div><i class="fas fa-question-circle mr-1 w-4"></i>{{ $q->questions_count ?? $q->questions()->count() }} soal</div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-slate-500 py-12">Belum ada kuis</div>
        @endforelse
    </div>
    @if($quizzes->hasPages())<div class="px-6 py-4">{{ $quizzes->links() }}</div>@endif
</div>
@endsection
