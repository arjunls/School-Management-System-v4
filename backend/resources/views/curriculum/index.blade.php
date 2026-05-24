@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Capaian Pembelajaran (CP)</h1>
        <a href="{{ route('curriculum.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Tambah CP</a>
    </div>

    <form method="GET" class="flex flex-wrap gap-3">
        <select name="subject_id" class="rounded-lg border border-slate-300 px-4 py-2 text-sm" onchange="this.form.submit()">
            <option value="">Semua Mapel</option>
            @foreach($subjects as $subject)
            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
            @endforeach
        </select>
        <select name="phase" class="rounded-lg border border-slate-300 px-4 py-2 text-sm" onchange="this.form.submit()">
            <option value="">Semua Fase</option>
            @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $phase)
            <option value="{{ $phase }}" {{ request('phase') == $phase ? 'selected' : '' }}>Fase {{ $phase }}</option>
            @endforeach
        </select>
        <a href="{{ route('curriculum.index') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-900">Reset</a>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Kode</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Mapel</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Deskripsi</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Fase</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Kelas</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($cps as $cp)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $cp->code }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $cp->subject->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-slate-600 max-w-xs truncate">{{ $cp->description }}</td>
                    <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Fase {{ $cp->phase }}</span></td>
                    <td class="px-4 py-3 text-center text-slate-600">{{ $cp->class ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('curriculum.show', $cp) }}" class="text-blue-600 hover:text-blue-800" title="Lihat"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('curriculum.edit', $cp) }}" class="text-indigo-600 hover:text-indigo-800" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('curriculum.destroy', $cp) }}" method="POST" onsubmit="return confirm('Hapus CP?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-slate-500 py-8">Belum ada CP</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($cps->hasPages())<div class="px-6 py-4">{{ $cps->links() }}</div>@endif
</div>
@endsection
