@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('alumni.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($alumni) ? 'Edit Alumni' : 'Tambah Alumni' }}</h1></div>
    <form method="POST" action="{{ isset($alumni) ? route('alumni.update', $alumni) : route('alumni.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($alumni)) @method('PUT') @endif
        @if(!isset($alumni))
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Siswa <span class="text-red-500">*</span></label><select name="student_id" required class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih Siswa</option>@foreach($students as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nisn }})</option>@endforeach</select></div>
        @endif
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tahun Lulus</label><input type="text" name="graduation_year" value="{{ old('graduation_year', $alumni->graduation_year ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status Akhir</label><select name="final_status" class="w-full rounded-lg border border-slate-300 px-4 py-2"><option value="">Pilih</option><option value="lulus" {{ old('final_status', $alumni->final_status ?? '') == 'lulus' ? 'selected' : '' }}>Lulus</option><option value="mutasi" {{ old('final_status', $alumni->final_status ?? '') == 'mutasi' ? 'selected' : '' }}>Mutasi</option><option value="drop_out" {{ old('final_status', $alumni->final_status ?? '') == 'drop_out' ? 'selected' : '' }}>Drop Out</option><option value="meninggal" {{ old('final_status', $alumni->final_status ?? '') == 'meninggal' ? 'selected' : '' }}>Meninggal</option></select></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Pekerjaan Saat Ini</label><input type="text" name="current_occupation" value="{{ old('current_occupation', $alumni->current_occupation ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Perusahaan</label><input type="text" name="current_company" value="{{ old('current_company', $alumni->current_company ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Pendidikan Lanjutan</label><input type="text" name="current_education" value="{{ old('current_education', $alumni->current_education ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">No. Telepon</label><input type="text" name="phone" value="{{ old('phone', $alumni->phone ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Email</label><input type="email" name="email" value="{{ old('email', $alumni->email ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label><textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('address', $alumni->address ?? '') }}</textarea></div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($alumni) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
