@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">PKL / Prakerin</h1><a href="{{ route('pkl.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Tambah PKL</a></div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Siswa</th><th class="text-left px-4 py-3 font-medium">Perusahaan</th><th class="text-left px-4 py-3 font-medium">Pembimbing</th><th class="text-left px-4 py-3 font-medium">Mulai</th><th class="text-left px-4 py-3 font-medium">Selesai</th><th class="text-left px-4 py-3 font-medium">Status</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $r)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $r->student->name }}</td>
                        <td class="px-4 py-3">{{ $r->company_name }}</td>
                        <td class="px-4 py-3">{{ $r->supervisor_name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $r->start_date ? \Carbon\Carbon::parse($r->start_date)->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-3">{{ $r->end_date ? \Carbon\Carbon::parse($r->end_date)->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $r->status === 'completed' ? 'bg-green-100 text-green-800' : ($r->status === 'active' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">{{ $r->status }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('pkl.edit', $r) }}" class="text-amber-600 hover:text-amber-800 text-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('pkl.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-slate-500 py-12">Belum ada data PKL</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="px-6 py-4">{{ $records->links() }}</div>@endif
</div>
@endsection
