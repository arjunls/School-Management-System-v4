@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ isset($jadwal) ? 'Edit Jadwal' : 'Tambah Jadwal Baru' }}</h1>
        <a href="{{ route('jadwal.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ isset($jadwal) ? route('jadwal.update', $jadwal) : route('jadwal.store') }}" method="POST" class="space-y-4">
            @csrf
            @isset($jadwal) @method('PUT') @endisset

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>
                    <select name="class_id" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Kelas</option>
                        @foreach($kelas as $k)
                        <option value="{{ $k->id }}" @selected(old('class_id', $jadwal->class_id ?? '') == $k->id)>{{ $k->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mata Pelajaran</label>
                    <select name="subject_id" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Mapel</option>
                        @foreach($subjects as $s)
                        <option value="{{ $s->id }}" @selected(old('subject_id', $jadwal->subject_id ?? '') == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Guru</label>
                    <select name="teacher_id" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Guru</option>
                        @foreach($teachers as $t)
                        <option value="{{ $t->id }}" @selected(old('teacher_id', $jadwal->teacher_id ?? '') == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Hari</label>
                    <select name="day_of_week" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                        <option value="{{ $day }}" @selected(old('day_of_week', $jadwal->day_of_week ?? '') === $day)>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jam Mulai</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $jadwal->start_time ?? '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jam Selesai</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $jadwal->end_time ?? '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ruang</label>
                    <input type="text" name="room" value="{{ old('room', $jadwal->room ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">{{ isset($jadwal) ? 'Perbarui' : 'Simpan' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
