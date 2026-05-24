@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('budget.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">{{ isset($category) ? 'Edit Kategori' : 'Tambah Kategori Anggaran' }}</h1></div>
    <form method="POST" action="{{ isset($category) ? route('budget.update.category', $category) : route('budget.store.category') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf @if(isset($category)) @method('PUT') @endif
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label><input type="text" name="name" required value="{{ old('name', $category->name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Sumber Dana</label><input type="text" name="source" value="{{ old('source', $category->source ?? '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-2" placeholder="BOS/BOSDA/Lainnya"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2">{{ old('description', $category->description ?? '') }}</textarea></div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">{{ isset($category) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
