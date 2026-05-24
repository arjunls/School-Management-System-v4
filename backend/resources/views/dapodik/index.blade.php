@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Integrasi Dapodik</h1>
    </div>

    <!-- Sync Buttons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <form action="{{ route('dapodik.sync', 'peserta_didik') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition-shadow text-left">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Peserta Didik</p>
                        <p class="text-sm text-slate-500">Sinkronasi siswa</p>
                    </div>
                </div>
            </button>
        </form>

        <form action="{{ route('dapodik.sync', 'gtk') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition-shadow text-left">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher text-emerald-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">GTK</p>
                        <p class="text-sm text-slate-500">Guru & Tenaga Kependidikan</p>
                    </div>
                </div>
            </button>
        </form>

        <form action="{{ route('dapodik.sync', 'rombel') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition-shadow text-left">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-amber-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Rombel</p>
                        <p class="text-sm text-slate-500">Rombongan Belajar</p>
                    </div>
                </div>
            </button>
        </form>

        <form action="{{ route('dapodik.sync', 'sarana') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition-shadow text-left">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-school text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Sarpras</p>
                        <p class="text-sm text-slate-500">Sarana & Prasarana</p>
                    </div>
                </div>
            </button>
        </form>
    </div>

    <!-- Config Form -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Konfigurasi Dapodik</h2>
        <form action="{{ route('dapodik.config') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">NPSN</label>
                    <input type="text" name="npsn" value="{{ $npsn }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan NPSN">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">API URL</label>
                    <input type="url" name="dapodik_base_url" value="{{ $dapodikBaseUrl }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://dapodik.example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">API Key</label>
                    <input type="text" name="dapodik_api_key" value="{{ $dapodikApiKey }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan API Key">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>

    <!-- Sync Logs -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900">Riwayat Sinkronasi</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Records</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Mulai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Selesai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Pesan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm text-slate-900">{{ $log->sync_type }}</td>
                        <td class="px-6 py-4">
                            @if($log->status === 'success')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Berhasil</span>
                            @elseif($log->status === 'failed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Gagal</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Proses</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ number_format($log->records_processed) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $log->started_at ? \Carbon\Carbon::parse($log->started_at)->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $log->completed_at ? \Carbon\Carbon::parse($log->completed_at)->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate">{{ $log->message ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada riwayat sinkronasi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
