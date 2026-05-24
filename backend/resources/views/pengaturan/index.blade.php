@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <h1 class="text-2xl font-bold text-slate-900">{{ __('common.pengaturan') }}</h1>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('pengaturan.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-semibold text-slate-900 border-b pb-2">Profil Sekolah</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Sekolah</label>
                    <input type="text" name="name" value="{{ old('name', $profile->name) }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">NPSN</label>
                    <input type="text" name="npsn" value="{{ old('npsn', $profile->npsn ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $profile->email ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $profile->phone ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Website</label>
                    <input type="text" name="website" value="{{ old('website', $profile->website ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kepala Sekolah</label>
                    <input type="text" name="kepala_sekolah" value="{{ old('kepala_sekolah', $profile->kepala_sekolah ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Akreditasi</label>
                    <select name="akreditasi" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        @foreach(['A', 'B', 'C', 'Unggul', 'Baik Sekali', 'Baik'] as $a)
                        <option value="{{ $a }}" @selected(old('akreditasi', $profile->akreditasi ?? '') === $a)>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                <textarea name="address" rows="2" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address', $profile->address ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $profile->description ?? '') }}</textarea>
            </div>

            <h2 class="text-lg font-semibold text-slate-900 border-b pb-2 pt-4">Preferensi Aplikasi</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mode Gelap</label>
                    <select name="dark_mode" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="false" @selected(old('dark_mode', $darkMode) === 'false')>Terang</option>
                        <option value="true" @selected(old('dark_mode', $darkMode) === 'true')>Gelap</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Bahasa</label>
                    <select name="locale" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="id" @selected(old('locale', $locale) === 'id')>Indonesia</option>
                        <option value="en" @selected(old('locale', $locale) === 'en')>English</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>
@endsection
