@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('ukk.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($cert) ? 'Edit Sertifikasi' : 'Tambah Sertifikasi' }}</h1></div>
    <form method="POST" action="{{ isset($cert) ? route('ukk.update.cert', $cert) : route('ukk.store.cert') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($cert)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Skema <span class="text-red-500">*</span></label>
            <select name="schema_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2">
                <option value="">Pilih Skema</option>
                @foreach($schemas as $s)
                <option value="{{ $s->id }}" {{ old('schema_id', $cert->schema_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Siswa <span class="text-red-500">*</span></label>
            @if(isset($cert))
            <p class="text-sm text-slate-900 py-2">{{ $cert->student->name }}</p>
            @else
            <select name="student_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2">
                <option value="">Pilih Siswa</option>
                @foreach($students as $s)
                <option value="{{ $s->id }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->nisn ?? '-' }})</option>
                @endforeach
            </select>
            @endif
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Assesor</label>
            <select name="assessor_id" class="w-full rounded-lg border border-slate-300 px-4 py-2">
                <option value="">Pilih Assessor</option>
                @foreach($assessors as $a)
                <option value="{{ $a->id }}" {{ old('assessor_id', $cert->assessor_id ?? '') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Ujian</label><input type="date" name="exam_date" value="{{ old('exam_date', isset($cert) && $cert->exam_date ? $cert->exam_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Hasil</label><input type="text" name="result" value="{{ old('result', $cert->result ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2" placeholder="Kompeten / Tidak Kompeten"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">No. Sertifikat</label><input type="text" name="certificate_number" value="{{ old('certificate_number', $cert->certificate_number ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="registered" {{ old('status', $cert->status ?? 'registered') == 'registered' ? 'selected' : '' }}>Terdaftar</option><option value="passed" {{ old('status', $cert->status ?? 'registered') == 'passed' ? 'selected' : '' }}>Lulus</option><option value="failed" {{ old('status', $cert->status ?? 'registered') == 'failed' ? 'selected' : '' }}>Tidak Lulus</option></select></div>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($cert) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
