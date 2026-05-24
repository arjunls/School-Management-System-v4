<?php

namespace App\Modules\Asset\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Asset\Models\Asset;
use App\Modules\Asset\Models\AssetCategory;
use App\Modules\Asset\Models\AssetLoan;
use App\Modules\Asset\Models\Consumable;
use Illuminate\Http\Request;

class AssetWebController extends Controller
{
    public function index()
    {
        $totalAset = Asset::count();
        $dipinjam = Asset::where('status', 'borrowed')->count();
        $tersedia = Asset::where('status', 'available')->count();
        $perawatan = Asset::where('status', 'maintenance')->count();
        $totalKategori = AssetCategory::count();
        $lowStock = Consumable::whereColumn('stock', '<=', 'min_stock')->get();
        $recentLoans = AssetLoan::with(['asset', 'borrower'])->latest()->take(5)->get();
        $recentAssets = Asset::with('category')->latest()->take(5)->get();

        return view('asset.index', compact(
            'totalAset', 'dipinjam', 'tersedia', 'perawatan',
            'totalKategori', 'lowStock', 'recentLoans', 'recentAssets'
        ));
    }

    public function categories()
    {
        $categories = AssetCategory::withCount('assets')->orderBy('name')->paginate(25);
        return view('asset.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        AssetCategory::create($data);

        return redirect()->route('asset.categories')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, AssetCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($data);

        return redirect()->route('asset.categories')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory(AssetCategory $category)
    {
        if ($category->assets()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki aset.');
        }
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function assets(Request $request)
    {
        $query = Asset::with('category');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $assets = $query->orderBy('name')->paginate(25);
        $categories = AssetCategory::orderBy('name')->get();

        return view('asset.assets', compact('assets', 'categories'));
    }

    public function createAsset()
    {
        $categories = AssetCategory::orderBy('name')->get();
        return view('asset.asset-form', compact('categories'));
    }

    public function storeAsset(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:assets,code',
            'category_id' => 'required|exists:asset_categories,id',
            'description' => 'nullable|string|max:5000',
            'location' => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'condition' => 'nullable|string|max:100',
            'status' => 'required|in:available,borrowed,maintenance,retired',
            'image' => 'nullable|string|max:255',
        ]);

        Asset::create($data);

        return redirect()->route('asset.assets')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function editAsset(Asset $asset)
    {
        $categories = AssetCategory::orderBy('name')->get();
        return view('asset.asset-form', compact('asset', 'categories'));
    }

    public function updateAsset(Request $request, Asset $asset)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:assets,code,' . $asset->id,
            'category_id' => 'required|exists:asset_categories,id',
            'description' => 'nullable|string|max:5000',
            'location' => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'condition' => 'nullable|string|max:100',
            'status' => 'required|in:available,borrowed,maintenance,retired',
            'image' => 'nullable|string|max:255',
        ]);

        $asset->update($data);

        return redirect()->route('asset.assets')->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroyAsset(Asset $asset)
    {
        $asset->delete();
        return back()->with('success', 'Aset berhasil dihapus.');
    }

    public function loans(Request $request)
    {
        $query = AssetLoan::with(['asset', 'borrower']);

        if ($search = $request->get('search')) {
            $query->whereHas('borrower', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('asset', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $loans = $query->latest()->paginate(25);
        $assets = Asset::where('status', 'available')->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('asset.loans', compact('loans', 'assets', 'users'));
    }

    public function storeLoan(Request $request)
    {
        $data = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'borrower_id' => 'required|exists:users,id',
            'borrow_date' => 'required|date',
            'purpose' => 'required|string|max:2000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $asset = Asset::findOrFail($data['asset_id']);
        if ($asset->status !== 'available') {
            return back()->with('error', 'Aset sedang tidak tersedia untuk dipinjam.');
        }

        $data['status'] = 'borrowed';
        AssetLoan::create($data);
        $asset->update(['status' => 'borrowed']);

        return redirect()->route('asset.loans')->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function returnLoan(AssetLoan $loan)
    {
        if ($loan->status !== 'borrowed') {
            return back()->with('error', 'Aset sudah dikembalikan.');
        }

        $loan->update([
            'return_date' => now(),
            'status' => 'returned',
        ]);

        $loan->asset->update(['status' => 'available']);

        return back()->with('success', 'Aset berhasil dikembalikan.');
    }

    public function destroyLoan(AssetLoan $loan)
    {
        $loan->delete();
        return back()->with('success', 'Data peminjaman berhasil dihapus.');
    }

    public function consumables(Request $request)
    {
        $query = Consumable::orderBy('name');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        $consumables = $query->paginate(25);
        $categories = Consumable::select('category')->distinct()->whereNotNull('category')->pluck('category');
        $lowStockCount = Consumable::whereColumn('stock', '<=', 'min_stock')->count();

        return view('asset.consumables', compact('consumables', 'categories', 'lowStockCount'));
    }

    public function storeConsumable(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
        ]);

        Consumable::create($data);

        return redirect()->route('asset.consumables')->with('success', 'Barang habis pakai berhasil ditambahkan.');
    }

    public function updateConsumable(Request $request, Consumable $consumable)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
        ]);

        $consumable->update($data);

        return redirect()->route('asset.consumables')->with('success', 'Barang habis pakai berhasil diperbarui.');
    }

    public function destroyConsumable(Consumable $consumable)
    {
        $consumable->delete();
        return back()->with('success', 'Barang habis pakai berhasil dihapus.');
    }
}
