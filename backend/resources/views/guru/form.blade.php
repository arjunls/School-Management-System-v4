@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ isset($guru) ? 'Edit Guru' : 'Tambah Guru Baru' }}</h1>
        <a href="{{ route('guru.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ isset($guru) ? route('guru.update', $guru) : route('guru.store') }}" method="POST" class="space-y-4">
            @csrf
            @isset($guru) @method('PUT') @endisset

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $guru->name ?? '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $guru->email ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $guru->phone ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin</label>
                    <select name="gender" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Pilih</option>
                        <option value="male" @selected(old('gender', $guru->gender ?? '') === 'male')>Laki-laki</option>
                        <option value="female" @selected(old('gender', $guru->gender ?? '') === 'female')>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $guru->date_of_birth ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="active" @selected(old('status', $guru->status ?? 'active') === 'active')>Aktif</option>
                        <option value="inactive" @selected(old('status', $guru->status ?? '') === 'inactive')>Tidak Aktif</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="2" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('alamat', $guru->alamat ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">{{ isset($guru) ? 'Perbarui' : 'Simpan' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
