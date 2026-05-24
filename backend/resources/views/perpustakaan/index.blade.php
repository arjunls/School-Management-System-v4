@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Perpustakaan</h1><button onclick="document.getElementById('addBookModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"><i class="fas fa-plus mr-2"></i>Tambah Buku</button></div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-semibold text-slate-900">Daftar Buku</h2>
            <span class="text-sm text-slate-500">{{ $books->total() }} buku</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($books as $b)
            <div class="p-4 flex items-center justify-between hover:bg-slate-50">
                <div class="flex-1">
                    <p class="font-medium text-slate-900">{{ $b->title }}</p>
                    <p class="text-sm text-slate-500">{{ $b->author }}@if($b->isbn) &middot; {{ $b->isbn }}@endif @if($b->year) &middot; {{ $b->year }}@endif</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm {{ $b->stock > 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $b->stock ?? 0 }} tersedia</span>
                    <form action="{{ route('perpustakaan.destroy', $b) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button></form>
                </div>
            </div>
            @empty
            <div class="text-center text-slate-500 py-12">Belum ada buku</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200"><h2 class="font-semibold text-slate-900">Peminjaman Aktif</h2></div>
        <div class="divide-y divide-slate-100">
            @forelse($loans as $l)
            <div class="p-4 flex items-center justify-between hover:bg-slate-50">
                <div>
                    <p class="font-medium text-slate-900">{{ $l->book->title }}</p>
                    <p class="text-sm text-slate-500">Dipinjam oleh: {{ $l->user->name }} &middot; Jatuh tempo {{ \Carbon\Carbon::parse($l->due_date)->format('d M Y') }}</p>
                </div>
                <form action="{{ route('perpustakaan.return', $l) }}" method="POST">@csrf<button class="px-3 py-1 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-sm"><i class="fas fa-check mr-1"></i>Kembali</button></form>
            </div>
            @empty
            <div class="text-center text-slate-500 py-12">Tidak ada peminjaman aktif</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Pinjam Buku</h2>
        <form method="POST" action="{{ route('perpustakaan.borrow') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @csrf
            <select name="book_id" required class="rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"><option value="">Pilih Buku</option>@foreach($books as $b)<option value="{{ $b->id }}">{{ $b->title }}</option>@endforeach</select>
            <select name="user_id" required class="rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"><option value="">Pilih Peminjam</option>@foreach(\App\Models\User::whereIn('role',['student','teacher','staff'])->get() as $u)<option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>@endforeach</select>
            <div class="flex gap-2"><input type="date" name="due_date" required class="flex-1 rounded-lg border border-slate-300 px-4 py-2 focus:ring-2 focus:ring-blue-500"><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Pinjam</button></div>
        </form>
    </div>

    @if($books->hasPages())<div class="px-6 py-4">{{ $books->links() }}</div>@endif

    <div id="addBookModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6">
            <div class="flex items-center justify-between mb-4"><h2 class="text-lg font-bold text-slate-900">Tambah Buku</h2><button onclick="document.getElementById('addBookModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button></div>
            <form method="POST" action="{{ route('perpustakaan.store') }}" class="space-y-4">
                @csrf
                <div><label class="block text-sm font-medium text-slate-700">Judul <span class="text-red-500">*</span></label><input type="text" name="title" required class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-slate-700">Penulis</label><input type="text" name="author" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
                    <div><label class="block text-sm font-medium text-slate-700">ISBN</label><input type="text" name="isbn" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-slate-700">Penerbit</label><input type="text" name="publisher" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
                    <div><label class="block text-sm font-medium text-slate-700">Tahun</label><input type="number" name="year" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
                </div>
                <div><label class="block text-sm font-medium text-slate-700">Stok</label><input type="number" name="stock" value="1" min="0" class="w-full rounded-lg border border-slate-300 px-4 py-2"></div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
