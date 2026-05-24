@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('printing.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">Cetak Kartu Pelajar</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 max-w-lg">
        <form method="POST" action="{{ route('printing.kartu-pelajar') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Siswa</label>
                <select name="student_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->nisn ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-print mr-2"></i>Cetak PDF
            </button>
        </form>
    </div>
</div>
@endsection
