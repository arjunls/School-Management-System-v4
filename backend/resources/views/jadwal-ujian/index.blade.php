@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Jadwal Ujian</h1><a href="{{ route('jadwal-ujian.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Tambah Jadwal</a></div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Nama</th><th class="text-left px-4 py-3 font-medium">Kelas</th><th class="text-left px-4 py-3 font-medium">Mapel</th><th class="text-left px-4 py-3 font-medium">Tanggal</th><th class="text-left px-4 py-3 font-medium">Waktu</th><th class="text-left px-4 py-3 font-medium">Ruangan</th><th class="text-left px-4 py-3 font-medium">Tipe</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($schedules as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $s->name }}</td>
                        <td class="px-4 py-3">{{ $s->class->name }}</td>
                        <td class="px-4 py-3">{{ $s->subject->name }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($s->exam_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $s->start_time }} - {{ $s->end_time }}</td>
                        <td class="px-4 py-3">{{ $s->room ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $s->type === 'final' ? 'bg-red-100 text-red-800' : ($s->type === 'midterm' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">{{ $s->type }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('jadwal-ujian.edit', $s) }}" class="text-amber-600 hover:text-amber-800 text-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('jadwal-ujian.destroy', $s) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-slate-500 py-12">Belum ada jadwal ujian</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($schedules->hasPages())<div class="px-6 py-4">{{ $schedules->links() }}</div>@endif
</div>
@endsection
