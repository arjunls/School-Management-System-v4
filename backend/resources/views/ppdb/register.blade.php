@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center space-y-6">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto">
            <i class="fas fa-check-circle text-3xl text-emerald-600"></i>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-bold text-slate-900">Pendaftaran Berhasil!</h1>
            <p class="text-slate-600">Terima kasih telah mendaftar. Berikut adalah nomor pendaftaran Anda:</p>
        </div>

        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-3">
            <div>
                <p class="text-sm text-slate-500">Nomor Pendaftaran</p>
                <p class="text-2xl font-bold text-blue-600 tracking-wider">{{ $applicant->registration_number }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-500">Nama Lengkap</p>
                    <p class="font-medium text-slate-900">{{ $applicant->full_name }}</p>
                </div>
                <div>
                    <p class="text-slate-500">NISN</p>
                    <p class="font-medium text-slate-900">{{ $applicant->nisn }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Status</p>
                    <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Terdaftar</span>
                </div>
                <div>
                    <p class="text-slate-500">Tanggal Daftar</p>
                    <p class="font-medium text-slate-900">{{ $applicant->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>
            Simpan nomor pendaftaran Anda untuk memantau status pendaftaran.
        </div>

        <a href="{{ route('ppdb.index') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Kembali
        </a>
    </div>
</div>
@endsection
