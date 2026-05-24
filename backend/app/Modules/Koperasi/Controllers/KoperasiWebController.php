<?php

namespace App\Modules\Koperasi\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Koperasi\Models\CooperativeProduct;
use App\Modules\Koperasi\Models\CooperativeSale;
use App\Modules\Koperasi\Models\CooperativeSaving;
use App\Modules\Koperasi\Models\CooperativeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KoperasiWebController extends Controller
{
    public function index()
    {
        $totalProducts = CooperativeProduct::count();
        $activeProducts = CooperativeProduct::where('status', 'active')->count();
        $todaySales = CooperativeSale::today()->sum('total_price');
        $todaySalesCount = CooperativeSale::today()->count();
        $totalSavingsBalance = CooperativeSaving::sum('balance');
        $totalMembers = CooperativeSaving::distinct('user_id')->count('user_id');
        $recentSales = CooperativeSale::with(['product', 'buyer'])
            ->orderBy('sold_at', 'desc')
            ->take(10)
            ->get();
        $lowStockProducts = CooperativeProduct::where('status', 'active')
            ->where('stock', '<=', 5)
            ->get();

        return view('koperasi.index', compact(
            'totalProducts', 'activeProducts', 'todaySales', 'todaySalesCount',
            'totalSavingsBalance', 'totalMembers', 'recentSales', 'lowStockProducts'
        ));
    }

    public function productsIndex()
    {
        $products = CooperativeProduct::withCount('sales')
            ->orderBy('name')
            ->paginate(25);
        return view('koperasi.products.index', compact('products'));
    }

    public function productsCreate()
    {
        return view('koperasi.products.form');
    }

    public function productsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'category' => 'required|string|max:100',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('koperasi/products', 'public');
        }

        CooperativeProduct::create($validated);

        return redirect()->route('koperasi.products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function productsEdit(CooperativeProduct $product)
    {
        return view('koperasi.products.form', compact('product'));
    }

    public function productsUpdate(Request $request, CooperativeProduct $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'category' => 'required|string|max:100',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('koperasi/products', 'public');
        }

        $product->update($validated);

        return redirect()->route('koperasi.products')->with('success', 'Produk berhasil diperbarui.');
    }

    public function productsDestroy(CooperativeProduct $product)
    {
        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('koperasi.products')->with('success', 'Produk berhasil dihapus.');
    }

    public function salesIndex()
    {
        $products = CooperativeProduct::where('status', 'active')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();
        $buyers = User::whereIn('role', ['siswa', 'guru', 'admin', 'tata-usaha'])
            ->orderBy('name')
            ->get();

        return view('koperasi.sales.index', compact('products', 'buyers'));
    }

    public function salesStore(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:cooperative_products,id',
            'quantity' => 'required|integer|min:1',
            'buyer_id' => 'required|exists:users,id',
        ]);

        $product = CooperativeProduct::findOrFail($validated['product_id']);

        if ($product->stock < $validated['quantity']) {
            return back()->with('error', "Stok tidak mencukupi. Stok tersedia: {$product->stock} {$product->unit}.");
        }

        if ($product->status !== 'active') {
            return back()->with('error', 'Produk tidak aktif.');
        }

        $totalPrice = $product->price * $validated['quantity'];

        DB::transaction(function () use ($product, $validated, $totalPrice) {
            $product->decrement('stock', $validated['quantity']);

            CooperativeSale::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'total_price' => $totalPrice,
                'buyer_id' => $validated['buyer_id'],
                'sold_at' => now(),
            ]);
        });

        return redirect()->route('koperasi.sales.history')
            ->with('success', 'Penjualan berhasil dicatat.');
    }

    public function salesHistory(Request $request)
    {
        $query = CooperativeSale::with(['product', 'buyer']);

        if ($request->filled('date_from')) {
            $query->whereDate('sold_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sold_at', '<=', $request->date_to);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $sales = $query->orderBy('sold_at', 'desc')->paginate(25);
        $products = CooperativeProduct::orderBy('name')->get();

        $grandTotal = $query->sum('total_price');

        return view('koperasi.sales.history', compact('sales', 'products', 'grandTotal'));
    }

    public function savingsIndex()
    {
        $savings = CooperativeSaving::with('user')
            ->orderBy('balance', 'desc')
            ->paginate(25);

        $totalBalance = CooperativeSaving::sum('balance');

        return view('koperasi.savings.index', compact('savings', 'totalBalance'));
    }

    public function savingsCreate(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:mandatory,voluntary',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        $exists = CooperativeSaving::where('user_id', $validated['user_id'])
            ->where('type', $validated['type'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Tabungan untuk user dan tipe ini sudah ada.');
        }

        $saving = CooperativeSaving::create([
            'user_id' => $validated['user_id'],
            'balance' => $validated['initial_balance'],
            'type' => $validated['type'],
        ]);

        if ($validated['initial_balance'] > 0) {
            CooperativeTransaction::create([
                'saving_id' => $saving->id,
                'amount' => $validated['initial_balance'],
                'type' => 'deposit',
                'description' => 'Saldo awal',
            ]);
        }

        return redirect()->route('koperasi.savings')->with('success', 'Tabungan berhasil dibuat.');
    }

    public function savingsTransaction(Request $request, CooperativeSaving $saving)
    {
        $validated = $request->validate([
            'type' => 'required|in:deposit,withdraw',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validated['type'] === 'withdraw' && $saving->balance < $validated['amount']) {
            return back()->with('error', 'Saldo tidak mencukupi untuk penarikan.');
        }

        DB::transaction(function () use ($saving, $validated) {
            if ($validated['type'] === 'deposit') {
                $saving->increment('balance', $validated['amount']);
            } else {
                $saving->decrement('balance', $validated['amount']);
            }

            CooperativeTransaction::create([
                'saving_id' => $saving->id,
                'amount' => $validated['amount'],
                'type' => $validated['type'],
                'description' => $validated['description'] ?? ($validated['type'] === 'deposit' ? 'Setoran' : 'Penarikan'),
            ]);
        });

        return redirect()->route('koperasi.savings.transactions', $saving)
            ->with('success', 'Transaksi berhasil dicatat.');
    }

    public function savingsTransactions(CooperativeSaving $saving)
    {
        $saving->load('user');
        $transactions = $saving->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('koperasi.savings.transactions', compact('saving', 'transactions'));
    }
}
