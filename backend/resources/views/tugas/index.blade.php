@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Tugas / LKPD Digital</h1><a href="{{ route('tugas.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Buat Tugas</a></div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Judul</th><th class="text-left px-4 py-3 font-medium">Kelas</th><th class="text-left px-4 py-3 font-medium">Mapel</th><th class="text-left px-4 py-3 font-medium">Guru</th><th class="text-left px-4 py-3 font-medium">Tenggat</th><th class="text-left px-4 py-3 font-medium">Nilai Maks</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assignments as $t)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $t->title }}</td>
                        <td class="px-4 py-3">{{ $t->class->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $t->subject->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $t->teacher->name ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="{{ now() > $t->due_date ? 'text-red-600' : 'text-slate-600' }}">{{ $t->due_date->format('d M Y H:i') }}</span></td>
                        <td class="px-4 py-3">{{ $t->max_score }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('tugas.submissions', $t) }}" class="text-blue-600 hover:text-blue-800 text-sm"><i class="fas fa-eye"></i> Pengumpulan</a>
                                <a href="{{ route('tugas.edit', $t) }}" class="text-amber-600 hover:text-amber-800 text-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('tugas.destroy', $t) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-slate-500 py-12">Belum ada tugas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($assignments->hasPages())<div class="px-6 py-4">{{ $assignments->links() }}</div>@endif
</div>
@endsection
