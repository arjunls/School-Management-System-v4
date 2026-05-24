@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Mata Pelajaran</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('mapel.create') }}" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Mapel
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">SKS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($subjects as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm font-mono text-slate-900">{{ $s->code }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900 font-medium">{{ $s->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900">{{ $s->credits ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900">{{ $s->teacher?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate">{{ $s->description ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('mapel.edit', $s) }}" class="text-sm text-green-600 hover:text-green-800 mr-3">Edit</a>
                            <form action="{{ route('mapel.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus {{ $s->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada mata pelajaran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subjects->hasPages())<div class="px-6 py-4 border-t">{{ $subjects->links() }}</div>@endif
    </div>
</div>
@endsection
