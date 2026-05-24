@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('website.pages.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">Edit Halaman</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 max-w-3xl">
        <form method="POST" action="{{ route('website.pages.update', $page) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Judul</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" required class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Konten</label>
                <textarea name="content" required rows="12" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">{{ old('content', $page->content) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Urutan</label>
                    <input type="number" name="order" value="{{ old('order', $page->order ?? 0) }}" min="0" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                </div>
                <div>
                    <label class="flex items-center gap-2 mt-6">
                        <input type="checkbox" name="is_published" value="1" {{ $page->is_published ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">Publikasikan</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection
