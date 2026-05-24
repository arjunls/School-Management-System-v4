@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Jurnal Mengajar</h1><a href="{{ route('jurnal.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Tambah Jurnal</a></div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Tanggal</th><th class="text-left px-4 py-3 font-medium">Kelas</th><th class="text-left px-4 py-3 font-medium">Mapel</th><th class="text-left px-4 py-3 font-medium">Guru</th><th class="text-left px-4 py-3 font-medium">Topik</th><th class="text-left px-4 py-3 font-medium">JP</th><th class="text-left px-4 py-3 font-medium">H/S</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $l)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($l->date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $l->class->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $l->subject->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $l->teacher->name ?? '-' }}</td>
                        <td class="px-4 py-3 max-w-xs truncate">{{ $l->topic ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $l->start_time && $l->end_time ? \Carbon\Carbon::parse($l->start_time)->diffInMinutes($l->end_time)/45 .' JP' : '-' }}</td>
                        <td class="px-4 py-3">{{ $l->present_students ?? '?' }}/{{ $l->absent_students ?? '?' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('jurnal.edit', $l) }}" class="text-amber-600 hover:text-amber-800 text-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('jurnal.destroy', $l) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-slate-500 py-12">Belum ada jurnal mengajar</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())<div class="px-6 py-4">{{ $logs->links() }}</div>@endif
</div>
@endsection
