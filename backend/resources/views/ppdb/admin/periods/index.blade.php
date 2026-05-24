@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Periode PPDB</h1>
        <a href="{{ route('ppdb.admin.periods.create') }}" class="mt-4 sm:mt-0 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i>Tambah Periode
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Tahun Ajaran</th>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Tanggal Mulai</th>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Tanggal Selesai</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Kuota</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Pendaftar</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Status</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($periods as $period)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $period->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $period->academic_year }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $period->start_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $period->end_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ $period->quota }}</td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ $period->applicants_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($period->status === 'active')
                                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-medium">Aktif</span>
                            @elseif($period->status === 'inactive')
                                <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Tidak Aktif</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Ditutup</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('ppdb.admin.periods.edit', $period) }}" class="text-sm text-green-600 hover:text-green-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('ppdb.admin.periods.destroy', $period) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus periode ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-slate-500 py-12">Belum ada periode PPDB.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
