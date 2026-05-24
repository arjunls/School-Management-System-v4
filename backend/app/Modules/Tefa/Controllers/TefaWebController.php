<?php namespace App\Modules\Tefa\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Tefa\Models\TefaProduct;
use App\Modules\Tefa\Models\TefaProduction;
use App\Modules\Tefa\Models\TefaSale;
use Illuminate\Http\Request;

class TefaWebController extends Controller
{
    public function index()
    {
        $productCount = TefaProduct::count();
        $totalSales = TefaSale::sum('total_price');
        $totalProduction = TefaProduction::sum('quantity');
        $lowStock = TefaProduct::where('stock', '<=', 5)->count();
        $recentSales = TefaSale::with('product')->orderBy('sale_date', 'desc')->take(5)->get();
        return view('tefa.index', compact(
            'productCount', 'totalSales', 'totalProduction', 'lowStock', 'recentSales'
        ));
    }

    public function products()
    {
        $products = TefaProduct::withCount('productions', 'sales')->orderBy('name')->paginate(10);
        return view('tefa.products', compact('products'));
    }

    public function createProduct()
    {
        return view('tefa.products-form');
    }

    public function storeProduct(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
        ]);
        TefaProduct::create($d);
        return redirect()->route('tefa.products')->with('success', 'Produk tersimpan');
    }

    public function editProduct(TefaProduct $product)
    {
        return view('tefa.products-form', compact('product'));
    }

    public function updateProduct(Request $r, TefaProduct $product)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
        ]);
        $product->update($d);
        return redirect()->route('tefa.products')->with('success', 'Produk diperbarui');
    }

    public function destroyProduct(TefaProduct $product)
    {
        $product->productions()->delete();
        $product->sales()->delete();
        $product->delete();
        return redirect()->route('tefa.products')->with('success', 'Produk dihapus');
    }

    public function productions()
    {
        $productions = TefaProduction::with('product')->orderBy('production_date', 'desc')->paginate(15);
        $products = TefaProduct::where('status', 'active')->orderBy('name')->get();
        return view('tefa.productions', compact('productions', 'products'));
    }

    public function storeProduction(Request $r)
    {
        $d = $r->validate([
            'product_id' => 'required|exists:tefa_products,id',
            'batch_no' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
            'status' => 'nullable|in:planned,in_progress,completed',
            'production_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        $prod = TefaProduction::create($d);
        TefaProduct::where('id', $d['product_id'])->increment('stock', $d['quantity']);
        return redirect()->route('tefa.productions')->with('success', 'Produksi tersimpan');
    }

    public function destroyProduction(TefaProduction $production)
    {
        TefaProduct::where('id', $production->product_id)->decrement('stock', $production->quantity);
        $production->delete();
        return redirect()->route('tefa.productions')->with('success', 'Data produksi dihapus');
    }

    public function sales()
    {
        $sales = TefaSale::with('product')->orderBy('sale_date', 'desc')->paginate(15);
        $products = TefaProduct::where('status', 'active')->orderBy('name')->get();
        return view('tefa.sales', compact('sales', 'products'));
    }

    public function createSale()
    {
        $products = TefaProduct::where('status', 'active')->where('stock', '>', 0)->orderBy('name')->get();
        return view('tefa.sales-form', compact('products'));
    }

    public function storeSale(Request $r)
    {
        $d = $r->validate([
            'product_id' => 'required|exists:tefa_products,id',
            'quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'sale_date' => 'nullable|date',
        ]);
        $product = TefaProduct::findOrFail($d['product_id']);
        if ($product->stock < $d['quantity']) {
            return back()->with('error', 'Stok tidak mencukupi. Stok saat ini: ' . $product->stock);
        }
        TefaSale::create($d);
        $product->decrement('stock', $d['quantity']);
        return redirect()->route('tefa.sales')->with('success', 'Penjualan tersimpan');
    }

    public function destroySale(TefaSale $sale)
    {
        TefaProduct::where('id', $sale->product_id)->increment('stock', $sale->quantity);
        $sale->delete();
        return redirect()->route('tefa.sales')->with('success', 'Data penjualan dihapus');
    }
}
