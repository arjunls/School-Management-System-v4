@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Detail Pendaftar</h1>
        <a href="{{ route('ppdb.admin.applicants') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Data Pribadi</h2>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium 
                        @if($applicant->status === 'registered') bg-yellow-100 text-yellow-800
                        @elseif($applicant->status === 'verified') bg-blue-100 text-blue-800
                        @elseif($applicant->status === 'accepted') bg-emerald-100 text-emerald-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($applicant->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-slate-500">No. Registrasi</p>
                        <p class="font-medium text-slate-900 font-mono">{{ $applicant->registration_number }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Periode</p>
                        <p class="font-medium text-slate-900">{{ $applicant->period?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Nama Lengkap</p>
                        <p class="font-medium text-slate-900">{{ $applicant->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">NISN</p>
                        <p class="font-medium text-slate-900">{{ $applicant->nisn }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Tempat, Tanggal Lahir</p>
                        <p class="font-medium text-slate-900">{{ $applicant->birth_place }}, {{ $applicant->birth_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Jenis Kelamin</p>
                        <p class="font-medium text-slate-900">{{ $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Agama</p>
                        <p class="font-medium text-slate-900">{{ $applicant->religion }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Sekolah Asal</p>
                        <p class="font-medium text-slate-900">{{ $applicant->previous_school }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Alamat</p>
                    <p class="font-medium text-slate-900">{{ $applicant->address }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-slate-500">No. Telepon</p>
                        <p class="font-medium text-slate-900">{{ $applicant->phone }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Email</p>
                        <p class="font-medium text-slate-900">{{ $applicant->email ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Data Orang Tua</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-slate-500">Nama Ayah</p>
                        <p class="font-medium text-slate-900">{{ $applicant->father_name }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Nama Ibu</p>
                        <p class="font-medium text-slate-900">{{ $applicant->mother_name }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">No. Telepon Orang Tua</p>
                        <p class="font-medium text-slate-900">{{ $applicant->parent_phone }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Ubah Status</h2>
                <form action="{{ route('ppdb.admin.applicants.update-status', $applicant) }}" method="POST" class="space-y-3">
                    @csrf @method('PUT')
                    <div>
                        <select name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="registered" @selected($applicant->status === 'registered')>Terdaftar</option>
                            <option value="verified" @selected($applicant->status === 'verified')>Terverifikasi</option>
                            <option value="accepted" @selected($applicant->status === 'accepted')>Diterima</option>
                            <option value="rejected" @selected($applicant->status === 'rejected')>Ditolak</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                        Perbarui Status
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3 text-sm">
                <h2 class="text-lg font-semibold text-slate-900">Informasi</h2>
                <div>
                    <p class="text-slate-500">Tanggal Daftar</p>
                    <p class="font-medium text-slate-900">{{ $applicant->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Terakhir Diperbarui</p>
                    <p class="font-medium text-slate-900">{{ $applicant->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
