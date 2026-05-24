@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Kelola Galeri</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Tambah Gambar</h2>
        <form method="POST" action="{{ route('website.galleries.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <input type="text" name="title" placeholder="Judul" required class="rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <input type="text" name="image_path" placeholder="Path / URL Gambar" required class="rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <input type="text" name="category" placeholder="Kategori" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm transition-colors">Tambah</button>
        </form>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($galleries as $gallery)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden group">
            <div class="h-40 bg-slate-100 relative">
                <img src="{{ $gallery->image_path }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-slate-300\'><i class=\'fas fa-image text-3xl\'></i></div>'">
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-slate-900 text-sm">{{ $gallery->title }}</h3>
                @if($gallery->category)<span class="text-xs text-indigo-600">{{ $gallery->category }}</span>@endif
                @if($gallery->description)<p class="text-xs text-slate-500 mt-1">{{ $gallery->description }}</p>@endif
            </div>
            <div class="px-4 pb-3">
                <form action="{{ route('website.galleries.destroy', $gallery) }}" method="POST" onsubmit="return confirm('Hapus galeri ini?')">
                    @csrf @method('DELETE')
                    <button class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash mr-1"></i>Hapus</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-slate-500 py-12">Belum ada gambar</div>
        @endforelse
    </div>

    @if($galleries->hasPages())<div class="px-6 py-4">{{ $galleries->links() }}</div>@endif
</div>
@endsection
