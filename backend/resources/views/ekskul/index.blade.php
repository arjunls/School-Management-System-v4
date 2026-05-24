@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Ekstrakurikuler</h1><a href="{{ route('ekskul.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Tambah Ekskul</a></div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($ekskuls as $e)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start justify-between mb-3">
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $e->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">{{ $e->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                <div class="flex gap-2">
                    <a href="{{ route('ekskul.edit', $e) }}" class="text-blue-500 hover:text-blue-700 text-sm"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('ekskul.destroy', $e) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                </div>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">{{ $e->name }}</h3>
            <p class="text-sm text-slate-600 mb-3">{{ Str::limit($e->description, 100) }}</p>
            <div class="space-y-1 text-xs text-slate-400">
                <div><i class="fas fa-user-tie mr-1 w-4"></i>Pembina: {{ $e->coach ?? '-' }}</div>
                <div><i class="fas fa-clock mr-1 w-4"></i>Jadwal: {{ $e->schedule ?? '-' }}</div>
                <div><i class="fas fa-map-marker-alt mr-1 w-4"></i>Lokasi: {{ $e->location ?? '-' }}</div>
                <div><i class="fas fa-users mr-1 w-4"></i>Anggota: {{ $e->members_count ?? '-' }}</div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-slate-500 py-12">Belum ada ekstrakurikuler</div>
        @endforelse
    </div>
    @if($ekskuls->hasPages())<div class="px-6 py-4">{{ $ekskuls->links() }}</div>@endif
</div>
@endsection
