@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('website.admin.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">{{ isset($post) ? 'Edit Postingan' : 'Buat Postingan' }}</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 max-w-3xl">
        <form method="POST" action="{{ isset($post) ? route('website.admin.update', $post) : route('website.admin.store') }}" class="space-y-4">
            @csrf
            @if(isset($post)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Judul</label>
                <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}" required class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Slug (kosongkan untuk auto-generate)</label>
                <input type="text" name="slug" value="{{ old('slug', $post->slug ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Konten</label>
                <textarea name="content" required rows="10" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">{{ old('content', $post->content ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Excerpt (ringkasan)</label>
                <textarea name="excerpt" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gambar Featured (URL/Path)</label>
                    <input type="text" name="featured_image" value="{{ old('featured_image', $post->featured_image ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                    <input type="text" name="category" value="{{ old('category', $post->category ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" value="1" {{ isset($post) && $post->is_published ? 'checked' : '' }}>
                    <span class="text-sm text-slate-700">Publikasikan</span>
                </label>
            </div>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                {{ isset($post) ? 'Simpan Perubahan' : 'Simpan' }}
            </button>
        </form>
    </div>
</div>
@endsection
