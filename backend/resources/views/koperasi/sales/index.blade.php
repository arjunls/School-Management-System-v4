@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">POS / Penjualan</h1>
        <a href="{{ route('koperasi.sales.history') }}" class="text-sm text-blue-600 hover:text-blue-800">Riwayat Penjualan</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('koperasi.sales.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Produk</label>
                <select name="product_id" id="product-select" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Pilih Produk</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}"
                        data-price="{{ $p->price }}"
                        data-stock="{{ $p->stock }}"
                        data-unit="{{ $p->unit }}">
                        {{ $p->name }} - Rp {{ number_format($p->price, 0, ',', '.') }} (Stok: {{ $p->stock }} {{ $p->unit }})
                    </option>
                    @endforeach
                </select>
                @error('product_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('quantity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Satuan</label>
                    <input type="text" id="unit-display" readonly value="-" class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Total Harga</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-medium">Rp</span>
                        <input type="text" id="total-price-display" readonly value="0" class="w-full pl-10 pr-4 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-700 font-semibold">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pembeli</label>
                <select name="buyer_id" required class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Pilih Pembeli</option>
                    @foreach($buyers as $b)
                    <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->role ?? '-' }})</option>
                    @endforeach
                </select>
                @error('buyer_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                    <i class="fas fa-check mr-2"></i>Catat Penjualan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const productSelect = document.getElementById('product-select');
    const quantityInput = document.getElementById('quantity');
    const unitDisplay = document.getElementById('unit-display');
    const totalDisplay = document.getElementById('total-price-display');

    function updateTotal() {
        const selected = productSelect.options[productSelect.selectedIndex];
        if (selected && selected.value) {
            const price = parseFloat(selected.dataset.price) || 0;
            const unit = selected.dataset.unit || '-';
            const stock = parseInt(selected.dataset.stock) || 0;
            const qty = parseInt(quantityInput.value) || 1;
            unitDisplay.value = unit;
            if (qty > stock) {
                totalDisplay.value = 'Melebihi stok!';
                totalDisplay.classList.add('text-red-600');
            } else {
                totalDisplay.value = new Intl.NumberFormat('id-ID').format(price * qty);
                totalDisplay.classList.remove('text-red-600');
            }
        } else {
            unitDisplay.value = '-';
            totalDisplay.value = '0';
        }
    }

    productSelect.addEventListener('change', updateTotal);
    quantityInput.addEventListener('input', updateTotal);
</script>
@endpush
@endsection
