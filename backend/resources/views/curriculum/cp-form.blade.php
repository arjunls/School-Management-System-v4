@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('curriculum.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">{{ isset($cp) ? 'Edit CP' : 'Tambah CP' }}</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ isset($cp) ? route('curriculum.update', $cp) : route('curriculum.store') }}" class="space-y-4">
            @csrf
            @if(isset($cp)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Mata Pelajaran</label>
                <select name="subject_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ isset($cp) && $cp->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kode CP</label>
                <input type="text" name="code" value="{{ old('code', $cp->code ?? '') }}" required class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea name="description" required rows="4" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">{{ old('description', $cp->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Fase</label>
                    <select name="phase" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                        <option value="">-- Pilih Fase --</option>
                        @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $phase)
                        <option value="{{ $phase }}" {{ isset($cp) && $cp->phase == $phase ? 'selected' : '' }}>Fase {{ $phase }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>
                    <input type="text" name="class" value="{{ old('class', $cp->class ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                </div>
            </div>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                {{ isset($cp) ? 'Simpan Perubahan' : 'Simpan' }}
            </button>
        </form>
    </div>
</div>
@endsection
