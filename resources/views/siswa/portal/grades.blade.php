@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('siswa.portal.dashboard') }}" class="text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-2xl font-bold text-slate-900">Nilai Saya</h1>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nilai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Semester</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tahun Ajaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($grades as $g)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm text-slate-900">{{ $g->subject?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-lg text-sm font-semibold {{ $g->score >= 75 ? 'bg-green-100 text-green-800' : ($g->score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $g->score }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-900">{{ $g->term ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900">{{ $g->academic_year_id ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">Belum ada nilai</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($grades->hasPages())<div class="px-6 py-4 border-t">{{ $grades->links() }}</div>@endif
    </div>
</div>
@endsection
