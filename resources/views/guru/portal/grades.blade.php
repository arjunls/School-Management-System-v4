@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex items-center gap-3">
        <a href="{{ route('guru.portal.dashboard') }}" class="text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Input Nilai</h1>
            <p class="text-sm text-slate-500">{{ $kelas->name }}</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Mata Pelajaran</label>
            <select name="subject_id" class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg" onchange="this.form.submit()">
                <option value="">Pilih</option>
                @foreach($subjects as $s)
                <option value="{{ $s->id }}" @selected(($selectedSubject ?? '') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Semester</label>
            <select name="term_id" class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg" onchange="this.form.submit()">
                <option value="">Pilih</option>
                @foreach($terms as $t)
                <option value="{{ $t->id }}" @selected(($selectedTerm ?? '') == $t->id)>{{ $t->name }} ({{ $t->academicYear->name }})</option>
                @endforeach
            </select>
        </div>
        @if($selectedSubject && $selectedTerm)
        <button type="button" onclick="document.getElementById('gradeForm').classList.toggle('hidden')" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Input Nilai</button>
        @endif
    </form>

    @if($selectedSubject && $selectedTerm)
    <form id="gradeForm" method="POST" action="{{ route('guru.portal.grades.store', $kelas) }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 {{ $errors->any() ? '' : 'hidden' }}">
        @csrf
        <input type="hidden" name="subject_id" value="{{ $selectedSubject }}">
        <input type="hidden" name="term_id" value="{{ $selectedTerm }}">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Nama Siswa</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-slate-500 uppercase">Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($students as $i => $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-sm text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 text-sm text-slate-900">{{ $s->name }}</td>
                        <td class="px-4 py-2 text-center">
                            <input type="number" name="scores[{{ $s->id }}]" value="{{ old('scores.' . $s->id, isset($grades[$s->id]) && ($g = $grades[$s->id]->first()) ? $g->score : '') }}" min="0" max="100" class="w-20 px-2 py-1 text-center bg-slate-50 border border-slate-300 rounded-lg">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex justify-end mt-4">
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Simpan Nilai</button>
        </div>
    </form>
    @endif

    @if(!$selectedSubject || !$selectedTerm)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-3">Daftar Siswa</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">NISN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($students as $i => $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-sm text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 text-sm text-slate-900">{{ $s->name }}</td>
                        <td class="px-4 py-2 text-sm text-slate-700">{{ $s->nisn ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
