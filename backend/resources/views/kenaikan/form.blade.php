@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('kenaikan.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($move) ? 'Edit Kenaikan Kelas' : 'Kenaikan Kelas Baru' }}</h1></div>

    @if(!isset($move))
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Kenaikan Batch (Satu Kelas)</h2>
        <form method="POST" action="{{ route('kenaikan.process') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Dari Kelas <span class="text-red-500">*</span></label>
                    <select name="from_class_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                        <option value="">Pilih</option>
                        @foreach($classes as $k)
                        <option value="{{ $k->id }}">{{ $k->name }} ({{ $k->students->count() }} siswa)</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Ke Kelas <span class="text-red-500">*</span></label>
                    <select name="to_class_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                        <option value="">Pilih</option>
                        @foreach($classes as $k)
                        <option value="{{ $k->id }}">{{ $k->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Tahun Ajaran</label><input type="text" name="academic_year" class="w-full rounded-lg border border-slate-300 px-3 py-2" value="{{ date('Y') }}/{{ date('Y')+1 }}"></div>
            </div>
            <div x-data="{ students: [], fromClassId: null }">
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Siswa</label>
                <select name="from_class_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 mb-2" x-model="fromClassId" @change="fetch('/api/kelas/'+fromClassId+'/students').then(r=>r.json()).then(d=>students=d.data||[])">
                    <option value="">-- Pilih Kelas Dulu --</option>
                    @foreach($classes as $k)
                    <option value="{{ $k->id }}">{{ $k->name }}</option>
                    @endforeach
                </select>
                <div class="max-h-40 overflow-y-auto border border-slate-200 rounded-lg p-2 space-y-1">
                    @foreach($students as $s)
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="student_ids[]" value="{{ $s->id }}" class="rounded border-slate-300"> {{ $s->name }}</label>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-users mr-2"></i>Proses Kenaikan Batch</button>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">{{ isset($move) ? '' : 'Atau Input Manual' }}</h2>
        <form method="POST" action="{{ isset($move) ? route('kenaikan.update', $move) : route('kenaikan.store') }}" class="space-y-4">
            @csrf @if(isset($move)) @method('PUT') @endif
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Siswa <span class="text-red-500">*</span></label>
                @if(isset($move))
                <p class="text-sm text-slate-900 py-2">{{ $move->student->name }}</p>
                @else
                <select name="student_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2">
                    <option value="">Pilih Siswa</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->kelas->name ?? '-' }})</option>
                    @endforeach
                </select>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Dari Kelas <span class="text-red-500">*</span></label>
                    <select name="from_class_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2">
                        <option value="">Pilih</option>
                        @foreach($classes as $k)
                        <option value="{{ $k->id }}" {{ old('from_class_id', $move->from_class_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Ke Kelas <span class="text-red-500">*</span></label>
                    <select name="to_class_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2">
                        <option value="">Pilih</option>
                        @foreach($classes as $k)
                        <option value="{{ $k->id }}" {{ old('to_class_id', $move->to_class_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Tahun Ajaran</label><input type="text" name="academic_year" value="{{ old('academic_year', $move->academic_year ?? date('Y').'/'.(date('Y')+1)) }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="pending" {{ old('status', $move->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option><option value="approved" {{ old('status', $move->status ?? 'pending') == 'approved' ? 'selected' : '' }}>Disetujui</option><option value="rejected" {{ old('status', $move->status ?? 'pending') == 'rejected' ? 'selected' : '' }}>Ditolak</option></select></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Alasan</label><textarea name="reason" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('reason', $move->reason ?? '') }}</textarea></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Lulus?</label>
                    <select name="is_graduated" class="w-full rounded-lg border border-slate-300 px-4 py-2">
                        <option value="0" {{ old('is_graduated', $move->is_graduated ?? '0') == '0' ? 'selected' : '' }}>Tidak</option>
                        <option value="1" {{ old('is_graduated', $move->is_graduated ?? '0') == '1' ? 'selected' : '' }}>Ya (Lulus)</option>
                    </select>
                    <label class="mt-2 flex items-center gap-2 text-sm"><input type="checkbox" name="update_class" value="1" checked class="rounded border-slate-300"> Update kelas siswa</label>
                </div>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($move) ? 'Update' : 'Simpan' }}</button>
        </form>
    </div>
</div>
@endsection
