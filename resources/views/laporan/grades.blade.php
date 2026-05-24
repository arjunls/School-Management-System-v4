@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan.index') }}" class="text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-2xl font-bold text-slate-900">Laporan Nilai</h1>
        </div>
        <a href="{{ route('export.nilai') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors"><i class="fas fa-download mr-2"></i>Export CSV</a>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Siswa</label>
            <select name="student_id" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                <option value="">Semua Siswa</option>
                @foreach($siswa as $s)
                <option value="{{ $s->id }}" @selected(request('student_id') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Mata Pelajaran</label>
            <select name="subject_id" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                <option value="">Semua Mapel</option>
                @foreach($subjects as $sj)
                <option value="{{ $sj->id }}" @selected(request('subject_id') == $sj->id)>{{ $sj->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">Filter</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nilai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Semester</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tahun Ajaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($grades as $g)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $g->student?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $g->subject?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-lg text-sm font-semibold {{ $g->score >= 75 ? 'bg-green-100 text-green-800' : ($g->score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $g->score }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $g->term ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $g->academic_year_id ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($grades->hasPages())<div class="px-6 py-4 border-t border-slate-200">{{ $grades->links() }}</div>@endif
    </div>
</div>
@endsection
