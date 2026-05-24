@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Pengumuman</h1>
        <a href="{{ route('pengumuman.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Buat Pengumuman</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($announcements as $a)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $a->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">{{ $a->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    <span class="text-xs text-slate-400">{{ $a->target_role ?? 'all' }}</span>
                </div>
                <form action="{{ route('pengumuman.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus?')">
                    @csrf @method('DELETE')
                    <button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                </form>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">{{ $a->title }}</h3>
            <p class="text-sm text-slate-600 mb-3">{{ Str::limit($a->content, 150) }}</p>
            <p class="text-xs text-slate-400">{{ $a->created_at->diffForHumans() }}</p>
        </div>
        @empty
        <div class="col-span-full text-center text-slate-500 py-12">Belum ada pengumuman</div>
        @endforelse
    </div>

    @if($announcements->hasPages())<div class="px-6 py-4">{{ $announcements->links() }}</div>@endif
</div>
@endsection
