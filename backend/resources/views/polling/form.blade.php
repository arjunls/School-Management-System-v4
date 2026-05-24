@extends('layouts.app')
@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div class="flex items-center gap-3"><a href="{{ route('polling.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">Buat Polling</h1></div>
    <form method="POST" action="{{ route('polling.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        @csrf
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Judul <span class="text-red-500">*</span></label><input type="text" name="title" required class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label><textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"></textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Mulai <span class="text-red-500">*</span></label><input type="date" name="start_at" required class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Selesai <span class="text-red-500">*</span></label><input type="date" name="end_at" required class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Opsi (min 2)</label>
            <div id="options-container" class="space-y-2">
                <div class="flex gap-2"><input type="text" name="options[]" placeholder="Opsi 1" required class="flex-1 rounded-lg border border-slate-300 px-4 py-2"><button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button></div>
                <div class="flex gap-2"><input type="text" name="options[]" placeholder="Opsi 2" required class="flex-1 rounded-lg border border-slate-300 px-4 py-2"><button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button></div>
            </div>
            <button type="button" onclick="addOption()" class="mt-2 text-sm text-blue-600 hover:text-blue-800"><i class="fas fa-plus mr-1"></i>Tambah Opsi</button>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Buat Polling</button>
    </form>
</div>
<script>
function addOption() {
    const c = document.getElementById('options-container');
    const n = c.children.length + 1;
    const d = document.createElement('div'); d.className = 'flex gap-2';
    d.innerHTML = '<input type="text" name="options[]" placeholder="Opsi '+n+'" required class="flex-1 rounded-lg border border-slate-300 px-4 py-2"><button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>';
    c.appendChild(d);
}
</script>
@endsection
