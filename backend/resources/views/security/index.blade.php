@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-900">Keamanan</h1>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-4">
                <i class="fas fa-shield-alt text-blue-600 mr-2"></i>Autentikasi 2FA
            </h2>
            @if($twoFactor && $twoFactor->is_enabled)
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-green-700 font-medium">2FA Aktif</span>
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="text-sm text-slate-600">
                    Terakhir digunakan: {{ $twoFactor->last_used_at ? $twoFactor->last_used_at->format('d/m/Y H:i') : 'Belum pernah' }}
                </div>
                <div class="space-y-2">
                    <p class="text-xs text-slate-500">Kode Cadangan:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($twoFactor->backup_codes ?? [] as $code)
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-mono">{{ $code }}</span>
                        @endforeach
                    </div>
                </div>
                <form action="{{ route('security.2fa.disable') }}" method="POST" onsubmit="return confirm('Nonaktifkan 2FA?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                        <i class="fas fa-times mr-2"></i>Nonaktifkan 2FA
                    </button>
                </form>
            </div>
            @else
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <span class="text-red-700 font-medium">2FA Tidak Aktif</span>
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
                <p class="text-sm text-slate-600">
                    Aktifkan 2FA untuk meningkatkan keamanan akun Anda.
                </p>
                <a href="{{ route('security.2fa.setup') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    <i class="fas fa-plus mr-2"></i>Aktifkan 2FA
                </a>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-4">
                <i class="fas fa-history text-emerald-600 mr-2"></i>Aktivitas Keamanan
            </h2>
            <div class="space-y-2">
                @forelse($securityLogs->take(5) as $log)
                <div class="flex items-center justify-between p-2 border border-slate-100 rounded">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-circle text-xs text-blue-500"></i>
                        <span class="text-sm">{{ $log->event }}</span>
                        <span class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    <span class="text-xs text-slate-500">{{ $log->ip_address }}</span>
                </div>
                @empty
                <p class="text-sm text-slate-500">Tidak ada aktivitas keamanan</p>
                @endforelse
            </div>
            <a href="{{ route('security.activity') }}" class="mt-3 text-blue-600 hover:text-blue-800 text-xs">
                Lihat Semua Aktivitas →
            </a>
        </div>
    </div>
</div>
@endsection
