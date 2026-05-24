@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Kelola Halaman</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Judul</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Slug</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Status</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Urutan</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pages as $page)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $page->title }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $page->slug }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $page->is_published ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $page->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-slate-600">{{ $page->order ?? 0 }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('website.pages.edit', $page) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-slate-500 py-8">Belum ada halaman</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pages->hasPages())<div class="px-6 py-4">{{ $pages->links() }}</div>@endif
</div>
@endsection
