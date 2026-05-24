<?php

namespace App\Modules\Finance\Fee\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Finance\Fee\Models\FeeInvoice;
use App\Modules\Finance\Fee\Models\FeeType;
use App\Modules\Finance\Fee\Models\FeePayment;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function __construct(private PaymentGatewayService $gateway) {}

    public function index()
    {
        $invoices = FeeInvoice::with(['student', 'feeType', 'payments'])->orderBy('created_at', 'desc')->get();
        $feeTypes = FeeType::all();
        $students = User::where('role', 'siswa')->get();
        return view('pembayaran.index', compact('invoices', 'feeTypes', 'students'));
    }

    public function create()
    {
        $feeTypes = FeeType::all();
        $students = User::where('role', 'siswa')->get();
        return view('pembayaran.create', compact('feeTypes', 'students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fee_type_id' => 'required|exists:fee_types,id',
            'student_id' => 'required|exists:users,id',
            'amount' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $feeType = FeeType::findOrFail($data['fee_type_id']);
        $data['amount'] ??= $feeType->amount;

        FeeInvoice::create($data);
        return redirect()->route('pembayaran.index')->with('success', 'Tagihan berhasil dibuat');
    }

    public function pay(Request $request, FeeInvoice $invoice)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,cheque,other',
            'reference_no' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        FeePayment::create([
            'fee_invoice_id' => $invoice->id,
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'payment_method' => $data['payment_method'],
            'reference_no' => $data['reference_no'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $totalPaid = $invoice->getPaidAmount();
        if ($totalPaid >= $invoice->amount) {
            $invoice->update(['status' => 'paid']);
        } else {
            $invoice->update(['status' => 'partial']);
        }

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dicatat');
    }

    public function payOnline(FeeInvoice $invoice)
    {
        $snapToken = $this->gateway->createSnapToken($invoice);
        if (!$snapToken) {
            return redirect()->route('pembayaran.index')->with('error', 'Gateway pembayaran tidak tersedia');
        }
        return view('pembayaran.pay-online', compact('invoice', 'snapToken'));
    }

    public function notification(Request $request)
    {
        $this->gateway->handleNotification($request->all());
        return response()->json(['status' => 'ok']);
    }

    public function destroy(FeeInvoice $invoice)
    {
        $invoice->payments()->delete();
        $invoice->delete();
        return redirect()->route('pembayaran.index')->with('success', 'Tagihan berhasil dihapus');
    }
}
