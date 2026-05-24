@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Kenaikan Kelas</h1><a href="{{ route('kenaikan.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-plus mr-2"></i>Kenaikan Baru</a></div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Siswa</th><th class="text-left px-4 py-3 font-medium">Dari Kelas</th><th class="text-left px-4 py-3 font-medium">Ke Kelas</th><th class="text-left px-4 py-3 font-medium">Tahun Ajaran</th><th class="text-left px-4 py-3 font-medium">Lulus</th><th class="text-left px-4 py-3 font-medium">Status</th><th class="text-left px-4 py-3 font-medium">Disetujui</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($moves as $m)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $m->student->name }}</td>
                        <td class="px-4 py-3">{{ $m->fromClass->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $m->toClass->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $m->academic_year ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $m->is_graduated ? 'Ya' : 'Tidak' }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $m->status == 'approved' ? 'bg-green-100 text-green-800' : ($m->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ $m->status ?? 'pending' }}</span></td>
                        <td class="px-4 py-3">{{ $m->approvedBy->name ?? '-' }}</td>
                        <td class="px-4 py-3"><div class="flex gap-2"><a href="{{ route('kenaikan.edit', $m) }}" class="text-amber-600 hover:text-amber-800"><i class="fas fa-edit"></i></a><form action="{{ route('kenaikan.destroy', $m) }}" method="POST" onsubmit="return confirm('Hapus data kenaikan?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></div></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-slate-500 py-12">Belum ada data kenaikan kelas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($moves->hasPages())<div class="px-6 py-4">{{ $moves->links() }}</div>@endif
    </div>
</div>
@endsection
