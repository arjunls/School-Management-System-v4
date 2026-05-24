@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('siswa.portal.dashboard') }}" class="text-slate-600 hover:text-slate-900"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-2xl font-bold text-slate-900">Kehadiran Saya</h1>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($records as $r)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm text-slate-900">{{ \Carbon\Carbon::parse($r->date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $sc = ['hadir' => 'bg-green-100 text-green-800', 'sakit' => 'bg-yellow-100 text-yellow-800', 'izin' => 'bg-blue-100 text-blue-800', 'alpha' => 'bg-red-100 text-red-800']; @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc[$r->status] ?? '' }}">{{ ucfirst($r->status) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-900">{{ $r->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-6 py-12 text-center text-slate-500">Belum ada kehadiran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())<div class="px-6 py-4 border-t">{{ $records->links() }}</div>@endif
    </div>
</div>
@endsection
