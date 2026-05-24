@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><div class="flex items-center gap-3"><a href="{{ route('tefa.index') }}" class="text-slate-400 hover:text-slate-600"><i class="fas fa-arrow-left"></i></a><h1 class="text-2xl font-bold text-slate-900">Produksi TEFA</h1></div></div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-4">Catat Produksi Baru</h2>
        <form method="POST" action="{{ route('tefa.store.production') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Produk</label>
                <select name="product_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="">Pilih Produk</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} (stok: {{ $p->stock }})</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Batch No.</label><input type="text" name="batch_no" class="w-full rounded-lg border border-slate-300 px-3 py-2" value="{{ 'BATCH-'.date('Ymd').'-'.strtoupper(\Illuminate\Support\Str::random(4)) }}"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label><input type="number" name="quantity" required min="1" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Produksi</label><input type="date" name="production_date" class="w-full rounded-lg border border-slate-300 px-3 py-2" value="{{ date('Y-m-d') }}"></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="planned">Direncanakan</option>
                    <option value="in_progress">Proses</option>
                    <option value="completed" selected>Selesai</option>
                </select>
            </div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label><input type="text" name="notes" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
            <div class="md:col-span-3 flex justify-end"><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-save mr-1"></i>Simpan Produksi</button></div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Riwayat Produksi</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Batch</th><th class="text-left px-4 py-3 font-medium">Produk</th><th class="text-left px-4 py-3 font-medium">Jumlah</th><th class="text-left px-4 py-3 font-medium">Tanggal</th><th class="text-left px-4 py-3 font-medium">Status</th><th class="text-left px-4 py-3 font-medium">Catatan</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($productions as $pr)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $pr->batch_no ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $pr->product->name }}</td>
                        <td class="px-4 py-3">{{ $pr->quantity }}</td>
                        <td class="px-4 py-3">{{ $pr->production_date ? \Carbon\Carbon::parse($pr->production_date)->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $pr->status == 'completed' ? 'bg-green-100 text-green-800' : ($pr->status == 'in_progress' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800') }}">{{ $pr->status }}</span></td>
                        <td class="px-4 py-3 max-w-[150px] truncate">{{ $pr->notes ?? '-' }}</td>
                        <td class="px-4 py-3"><form action="{{ route('tefa.destroy.production', $pr) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-slate-500 py-12">Belum ada produksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($productions->hasPages())<div class="px-6 py-4">{{ $productions->links() }}</div>@endif
    </div>
</div>
@endsection
