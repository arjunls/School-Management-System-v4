@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('printing.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">Cetak Legger Nilai</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 max-w-lg">
        <form method="POST" action="{{ route('printing.legger') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>
                <select name="class_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Mata Pelajaran</label>
                <select name="subject_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
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
