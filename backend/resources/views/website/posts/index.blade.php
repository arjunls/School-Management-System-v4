@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Kelola Postingan</h1>
        <a href="{{ route('website.admin.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Buat Postingan</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Judul</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Kategori</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Penulis</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Status</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Tanggal</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($posts as $post)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $post->title }}</td>
                    <td class="px-4 py-3">{{ $post->category ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $post->author->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $post->is_published ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $post->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-xs text-slate-500">{{ $post->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('website.show', $post) }}" class="text-blue-600 hover:text-blue-800" title="Lihat"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('website.admin.edit', $post) }}" class="text-indigo-600 hover:text-indigo-800" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('website.admin.destroy', $post) }}" method="POST" onsubmit="return confirm('Hapus postingan?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-slate-500 py-8">Belum ada postingan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())<div class="px-6 py-4">{{ $posts->links() }}</div>@endif
</div>
@endsection
