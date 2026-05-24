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
        <h1 class="text-2xl font-bold text-slate-900">Backup Database</h1>
        <form action="{{ route('backup.create') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-database mr-2"></i>Buat Backup Baru
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Ukuran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($backups as $b)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm font-mono text-slate-900">{{ $b['name'] }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ number_format($b['size'] / 1024, 1) }} KB</td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ \Carbon\Carbon::createFromTimestamp($b['date'])->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('backup.download', $b['name']) }}" class="text-sm text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-download"></i></a>
                            <form action="{{ route('backup.destroy', $b['name']) }}" method="POST" class="inline" onsubmit="return confirm('Hapus backup ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">Belum ada backup</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
        <i class="fas fa-info-circle mr-2"></i>
        Backup menggunakan <code>mysqldump</code>. Pastikan mysqldump terinstall di server.
    </div>
</div>
@endsection
