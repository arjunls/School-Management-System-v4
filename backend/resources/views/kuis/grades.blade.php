@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('kuis.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">Penilaian: {{ $quiz->title }}</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-semibold text-slate-900">Daftar Peserta ({{ $attempts->total() }})</h2>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">Rata-rata: {{ number_format($attempts->avg('score'), 1) }}</span>
                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs">Lulus: {{ $attempts->where('score', '>=', $quiz->passing_score)->count() }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Mulai</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Selesai</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Skor</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($attempts as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $a->student->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs text-slate-600 text-center">{{ $a->started_at ? $a->started_at->format('H:i') : '-' }}</td>
                        <td class="px-6 py-4 text-xs text-slate-600 text-center">{{ $a->submitted_at ? $a->submitted_at->format('H:i') : '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-lg {{ ($a->score ?? 0) >= ($quiz->passing_score ?? 0) ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $a->score ?? '?' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                {{ $a->status === 'submitted' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $a->status === 'graded' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $a->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}">
                                {{ $a->status === 'submitted' ? 'Terkumpul' : ($a->status === 'graded' ? 'Dinilai' : 'Berjalan') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('kuis.gradeAttempt', $a) }}" class="text-blue-600 hover:text-blue-800 text-xs">
                                <i class="fas fa-edit"></i> Nilai
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada peserta</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attempts->hasPages())<div class="px-6 py-4 border-t">{{ $attempts->links() }}</div>@endif
    </div>
</div>
@endsection
