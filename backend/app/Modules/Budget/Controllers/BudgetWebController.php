<?php namespace App\Modules\Budget\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Budget\Models\BudgetCategory;
use App\Modules\Budget\Models\Budget;
use Illuminate\Http\Request;

class BudgetWebController extends Controller
{
    public function index()
    {
        $categories = BudgetCategory::withCount('budgets')->orderBy('name')->get();
        $budgets = Budget::with('category')->orderBy('period', 'desc')->paginate(20);
        $totalPlanned = Budget::sum('planned_amount');
        $totalRealized = Budget::sum('realized_amount');
        return view('budget.index', compact('categories', 'budgets', 'totalPlanned', 'totalRealized'));
    }

    public function createCategory()
    {
        return view('budget.category-form');
    }

    public function storeCategory(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        BudgetCategory::create($d);
        return redirect()->route('budget.index')->with('success', 'Kategori anggaran tersimpan');
    }

    public function editCategory(BudgetCategory $category)
    {
        return view('budget.category-form', compact('category'));
    }

    public function updateCategory(Request $r, BudgetCategory $category)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        $category->update($d);
        return redirect()->route('budget.index')->with('success', 'Kategori diperbarui');
    }

    public function destroyCategory(BudgetCategory $category)
    {
        $category->budgets()->delete();
        $category->delete();
        return redirect()->route('budget.index')->with('success', 'Kategori dihapus');
    }

    public function create()
    {
        $categories = BudgetCategory::orderBy('name')->get();
        return view('budget.form', compact('categories'));
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'category_id' => 'required|exists:budget_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'planned_amount' => 'required|numeric|min:0',
            'realized_amount' => 'nullable|numeric|min:0',
            'period' => 'nullable|string|max:50',
            'status' => 'nullable|in:planned,approved,realized',
        ]);
        Budget::create($d);
        return redirect()->route('budget.index')->with('success', 'Anggaran tersimpan');
    }

    public function edit(Budget $budget)
    {
        $categories = BudgetCategory::orderBy('name')->get();
        return view('budget.form', compact('budget', 'categories'));
    }

    public function update(Request $r, Budget $budget)
    {
        $d = $r->validate([
            'category_id' => 'required|exists:budget_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'planned_amount' => 'required|numeric|min:0',
            'realized_amount' => 'nullable|numeric|min:0',
            'period' => 'nullable|string|max:50',
            'status' => 'nullable|in:planned,approved,realized',
        ]);
        $budget->update($d);
        return redirect()->route('budget.index')->with('success', 'Anggaran diperbarui');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return redirect()->route('budget.index')->with('success', 'Anggaran dihapus');
    }
}
