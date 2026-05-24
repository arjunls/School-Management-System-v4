@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Pendaftar PPDB</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('ppdb.admin.applicants') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Periode</label>
                <select name="period_id" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Periode</option>
                    @foreach($periods as $p)
                    <option value="{{ $p->id }}" @selected(request('period_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-slate-600 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / No. Registrasi / NISN" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="{{ route('ppdb.admin.applicants') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-colors text-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">No. Registrasi</th>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">NISN</th>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Periode</th>
                        <th class="text-left px-4 py-3 font-medium text-slate-700">Asal Sekolah</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Status</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Tanggal Daftar</th>
                        <th class="text-center px-4 py-3 font-medium text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($applicants as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-sm text-blue-600">{{ $a->registration_number }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $a->full_name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $a->nisn }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $a->period?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $a->previous_school }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusColors = [
                                    'registered' => 'bg-yellow-100 text-yellow-800',
                                    'verified' => 'bg-blue-100 text-blue-800',
                                    'accepted' => 'bg-emerald-100 text-emerald-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$a->status] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-600 text-xs">{{ $a->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('ppdb.admin.applicants.show', $a) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-slate-500 py-12">Belum ada pendaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($applicants->hasPages())
    <div class="px-6 py-4">
        {{ $applicants->links() }}
    </div>
    @endif
</div>
@endsection
