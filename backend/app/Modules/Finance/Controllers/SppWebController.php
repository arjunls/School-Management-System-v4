<?php namespace App\Modules\Finance\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Finance\Fee\Models\FeeInvoice;
use App\Modules\Finance\Fee\Models\FeeType;
use App\Models\User;
use Illuminate\Http\Request;

class SppWebController extends Controller
{
    public function index()
    {
        $invoices = FeeInvoice::with(['student', 'feeType', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        $unpaidCount = FeeInvoice::where('status', '!=', 'paid')->count();
        $totalOutstanding = FeeInvoice::where('status', '!=', 'paid')->sum('amount');
        $stats = FeeInvoice::selectRaw("status, COUNT(*) as count, SUM(amount) as total")
            ->groupBy('status')->get();
        return view('spp.index', compact('invoices', 'unpaidCount', 'totalOutstanding', 'stats'));
    }

    public function generateForm()
    {
        $feeTypes = FeeType::orderBy('name')->get();
        return view('spp.generate-form', compact('feeTypes'));
    }

    public function generate(Request $r)
    {
        $d = $r->validate([
            'fee_type_id' => 'required|exists:fee_types,id',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2099',
            'due_date' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $feeType = FeeType::findOrFail($d['fee_type_id']);
        $month = $d['month'] ?? now()->month;
        $year = $d['year'] ?? now()->year;
        $amount = $d['amount'] ?? $feeType->amount;
        $dueDate = $d['due_date'] ?? now()->addMonth()->startOfMonth();

        $students = User::where('role', 'student')->where('status', 'active')->get();
        $created = 0;
        $skipped = 0;

        foreach ($students as $student) {
            $exists = FeeInvoice::where('student_id', $student->id)
                ->where('fee_type_id', $feeType->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            FeeInvoice::create([
                'fee_type_id' => $feeType->id,
                'student_id' => $student->id,
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => 'unpaid',
                'notes' => "Tagihan {$feeType->name} bulan " . \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y'),
            ]);
            $created++;
        }

        return redirect()->route('spp.index')
            ->with('success', "Berhasil membuat {$created} tagihan baru. {$skipped} sudah ada.");
    }

    public function studentInvoices(User $student)
    {
        $invoices = FeeInvoice::with(['feeType', 'payments'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('spp.index', compact('invoices', 'student'));
    }
}
