@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Barang Habis Pakai</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('asset.index') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Dashboard
            </a>
            @if($lowStockCount > 0)
            <span class="px-3 py-1.5 bg-red-100 text-red-800 rounded-lg text-sm font-medium flex items-center gap-1">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $lowStockCount }} Stok Menipis
            </span>
            @endif
            <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Barang
            </button>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-slate-500 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama barang..."
                class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Kategori</label>
            <select name="category" class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">Filter</button>
            @if(request()->anyFilled(['search', 'category']))
            <a href="{{ route('asset.consumables') }}" class="px-4 py-2 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded-lg hover:bg-red-50">Reset</a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Min. Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($consumables as $c)
                    <tr class="hover:bg-slate-50 {{ $c->stock <= $c->min_stock ? 'bg-red-50/50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $c->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $c->category ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            <span class="font-bold {{ $c->stock <= 0 ? 'text-red-600' : ($c->stock <= $c->min_stock ? 'text-orange-600' : 'text-green-600') }}">
                                {{ number_format($c->stock) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ number_format($c->min_stock) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $c->unit ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $stockColors = ['out' => 'bg-red-100 text-red-800', 'low' => 'bg-orange-100 text-orange-800', 'sufficient' => 'bg-green-100 text-green-800'];
                                $stockLabels = ['out' => 'Habis', 'low' => 'Menipis', 'sufficient' => 'Aman'];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stockColors[$c->stock_status] }}">
                                {{ $stockLabels[$c->stock_status] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-1">
                            <button type="button" onclick="openEditModal({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ addslashes($c->unit ?? '') }}', {{ $c->stock }}, {{ $c->min_stock }}, '{{ addslashes($c->category ?? '') }}')" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('asset.consumable.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('Hapus barang ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">Belum ada barang habis pakai</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($consumables->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $consumables->links() }}
        </div>
        @endif
    </div>
</div>

<div id="createModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Tambah Barang Habis Pakai</h3>
        <form action="{{ route('asset.consumable.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Kategori</label>
                <input type="text" name="category" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Satuan</label>
                    <input type="text" name="unit" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="pcs, kg, liter">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Stok Saat Ini <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" min="0" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Stok Minimum <span class="text-red-500">*</span></label>
                <input type="number" name="min_stock" min="0" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Edit Barang Habis Pakai</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="editName" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Kategori</label>
                <input type="text" name="category" id="editCategory" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Satuan</label>
                    <input type="text" name="unit" id="editUnit" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Stok <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" id="editStock" min="0" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-500 mb-1">Stok Minimum <span class="text-red-500">*</span></label>
                <input type="number" name="min_stock" id="editMinStock" min="0" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openCreateModal() { document.getElementById('createModal').classList.remove('hidden'); }
function closeCreateModal() { document.getElementById('createModal').classList.add('hidden'); }
function openEditModal(id, name, unit, stock, minStock, category) {
    document.getElementById('editForm').action = '/asset/consumables/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editUnit').value = unit;
    document.getElementById('editStock').value = stock;
    document.getElementById('editMinStock').value = minStock;
    document.getElementById('editCategory').value = category;
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
</script>
@endpush
