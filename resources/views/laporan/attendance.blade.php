@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan.index') }}" class="text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-2xl font-bold text-slate-900">Laporan Kehadiran</h1>
        </div>
        <a href="{{ route('export.kehadiran') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors"><i class="fas fa-download mr-2"></i>Export CSV</a>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                <option value="">Semua</option>
                <option value="hadir" @selected(request('status') === 'hadir')>Hadir</option>
                <option value="sakit" @selected(request('status') === 'sakit')>Sakit</option>
                <option value="izin" @selected(request('status') === 'izin')>Izin</option>
                <option value="alpha" @selected(request('status') === 'alpha')>Alpha</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">Filter</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($records as $r)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $r->student?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ \Carbon\Carbon::parse($r->date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $sc = ['hadir' => 'bg-green-100 text-green-800', 'sakit' => 'bg-yellow-100 text-yellow-800', 'izin' => 'bg-blue-100 text-blue-800', 'alpha' => 'bg-red-100 text-red-800']; @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sc[$r->status] ?? 'bg-slate-100' }}">{{ ucfirst($r->status) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $r->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())<div class="px-6 py-4 border-t border-slate-200">{{ $records->links() }}</div>@endif
    </div>
</div>
@endsection
