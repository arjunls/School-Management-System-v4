@extends('layouts.app')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
    <div class="flex items-center justify-between"><h1 class="text-2xl font-bold text-slate-900">Anggaran RKAS / BOS</h1><div class="flex gap-2"><a href="{{ route('budget.create.category') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-folder mr-2"></i>Tambah Kategori</a><a href="{{ route('budget.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700"><i class="fas fa-plus-circle mr-2"></i>Tambah Anggaran</a></div></div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Total Anggaran</p><p class="text-3xl font-bold text-slate-900">Rp {{ number_format($totalPlanned, 0, ',', '.') }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Terealisasi</p><p class="text-3xl font-bold text-emerald-600">Rp {{ number_format($totalRealized, 0, ',', '.') }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"><p class="text-sm text-slate-500">Sisa</p><p class="text-3xl font-bold text-amber-600">Rp {{ number_format($totalPlanned - $totalRealized, 0, ',', '.') }}</p></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Kategori</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Nama</th><th class="text-left px-4 py-3 font-medium">Sumber</th><th class="text-left px-4 py-3 font-medium">Item</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categories as $cat)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $cat->name }}</td>
                            <td class="px-4 py-3">{{ $cat->source ?? '-' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ $cat->budgets_count }}</span></td>
                            <td class="px-4 py-3"><div class="flex gap-2"><a href="{{ route('budget.edit.category', $cat) }}" class="text-amber-600 hover:text-amber-800"><i class="fas fa-edit"></i></a><form action="{{ route('budget.destroy.category', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></div></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-slate-500 py-8">Belum ada kategori</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><h2 class="font-semibold text-slate-900">Item Anggaran</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-4 py-3 font-medium">Nama</th><th class="text-left px-4 py-3 font-medium">Kategori</th><th class="text-left px-4 py-3 font-medium">Planned</th><th class="text-left px-4 py-3 font-medium">Realized</th><th class="text-left px-4 py-3 font-medium">Periode</th><th class="text-left px-4 py-3 font-medium"></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($budgets as $b)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $b->name }}</td>
                            <td class="px-4 py-3">{{ $b->category->name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($b->planned_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($b->realized_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $b->period ?? '-' }}</td>
                            <td class="px-4 py-3"><div class="flex gap-2"><a href="{{ route('budget.edit', $b) }}" class="text-amber-600 hover:text-amber-800"><i class="fas fa-edit"></i></a><form action="{{ route('budget.destroy', $b) }}" method="POST" onsubmit="return confirm('Hapus anggaran?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form></div></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-slate-500 py-8">Belum ada anggaran</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($budgets->hasPages())<div class="px-6 py-4">{{ $budgets->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection
