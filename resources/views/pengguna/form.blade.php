@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">{{ isset($pengguna) ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h1>
        <a href="{{ route('pengguna.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ isset($pengguna) ? route('pengguna.update', $pengguna) : route('pengguna.store') }}" method="POST" class="space-y-4">
            @csrf
            @isset($pengguna) @method('PUT') @endisset

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $pengguna->name ?? '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $pengguna->email ?? '') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" {{ isset($pengguna) ? '' : 'required' }} class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="{{ isset($pengguna) ? 'Kosongkan jika tidak diubah' : '' }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Role (DB)</label>
                    <select name="role" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        @foreach(['admin','teacher','student','parent','staff'] as $r)
                        <option value="{{ $r }}" @selected(old('role', $pengguna->role ?? '') === $r)>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Role (Spatie)</label>
                    <select name="spatie_role" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        @foreach($roles as $r)
                        <option value="{{ $r }}" @selected(old('spatie_role', $pengguna?->roles->first()?->name ?? '') === $r)>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active" @selected(old('status', $pengguna->status ?? 'active') === 'active')>Aktif</option>
                        <option value="inactive" @selected(old('status', $pengguna->status ?? '') === 'inactive')>Tidak Aktif</option>
                        <option value="suspended" @selected(old('status', $pengguna->status ?? '') === 'suspended')>Suspended</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $pengguna->phone ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin</label>
                    <select name="gender" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        <option value="male" @selected(old('gender', $pengguna->gender ?? '') === 'male')>Laki-laki</option>
                        <option value="female" @selected(old('gender', $pengguna->gender ?? '') === 'female')>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', isset($pengguna) && $pengguna->date_of_birth ? $pengguna->date_of_birth->format('Y-m-d') : '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @if(isset($pengguna) && $pengguna->role === 'student')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $pengguna->nisn ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" @selected(old('kelas_id', $pengguna->kelas_id ?? '') == $k->id)>{{ $k->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jurusan</label>
                    <input type="text" name="jurusan" value="{{ old('jurusan', $pengguna->jurusan ?? '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @endif
                @if(isset($pengguna) && $pengguna->role === 'parent')
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Hubungkan ke Siswa</label>
                    <select name="children[]" multiple class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" size="6">
                        @foreach($students as $s)
                        <option value="{{ $s->id }}" @selected(in_array($s->id, $parentStudents ?? []))>{{ $s->name }} ({{ $s->nisn ?? $s->id }} - {{ $s->kelas?->name ?? '-' }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Tekan Ctrl/Cmd untuk memilih lebih dari satu</p>
                </div>
                @endif
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">{{ isset($pengguna) ? 'Perbarui' : 'Simpan' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
