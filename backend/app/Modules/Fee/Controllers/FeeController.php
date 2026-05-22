<?php

namespace App\Modules\Fee\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Fee\Models\FeeInvoice;
use App\Modules\Fee\Models\FeePayment;
use App\Modules\Fee\Models\FeeType;
use Illuminate\Http\Request;
use App\Modules\Fee\Requests\StoreFeeTypeRequest;
use App\Modules\Fee\Requests\UpdateFeeTypeRequest;
use App\Modules\Fee\Requests\StoreInvoiceRequest;
use App\Modules\Fee\Requests\PayInvoiceRequest;

/**
 * @group Fees
 *
 * APIs for managing fees
 */
class FeeController extends Controller
{
    /**
     * List all fee types
     */
    public function types(Request $request)
    {
        return response()->json(['success' => true, 'data' => FeeType::orderBy('name')->get()]);
    }

    /**
     * Create a new fee type
     */
    public function typeStore(StoreFeeTypeRequest $request)
    {
        $type = FeeType::create($request->validated());
        return response()->json(['success' => true, 'data' => $type, 'message' => 'Fee type created'], 201);
    }

    /**
     * Update a fee type
     */
    public function typeUpdate(UpdateFeeTypeRequest $request, int $id)
    {
        $type = FeeType::findOrFail($id);
        $type->update($request->validated());
        return response()->json(['success' => true, 'data' => $type, 'message' => 'Fee type updated']);
    }

    /**
     * Delete a fee type
     */
    public function typeDelete(int $id)
    {
        FeeType::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Fee type deleted']);
    }

    // Invoices
    /**
     * List all fee invoices
     */
    public function invoices(Request $request)
    {
        $user = $request->user();
        $query = FeeInvoice::with(['feeType:id,name,amount', 'student:id,name,email']);

        if ($user->role === 'student') $query->where('student_id', $user->id);

        if ($s = $request->status) $query->where('status', $s);
        if ($sId = $request->student_id) $query->where('student_id', $sId);

        return response()->json(['success' => true, 'data' => $query->orderByDesc('created_at')->paginate($request->per_page ?? 20)]);
    }

    /**
     * Create a new fee invoice
     */
    public function invoiceStore(StoreInvoiceRequest $request)
    {
        $data = $request->validated();
        if (!isset($data['amount'])) {
            $type = FeeType::findOrFail($data['fee_type_id']);
            $data['amount'] = $type->amount;
        }

        $invoice = FeeInvoice::create($data);
        $invoice->load(['feeType:id,name,amount', 'student:id,name,email']);
        return response()->json(['success' => true, 'data' => $invoice, 'message' => 'Invoice created'], 201);
    }

    // Payments
    /**
     * Record a payment for an invoice
     */
    public function pay(PayInvoiceRequest $request, int $invoiceId)
    {
        $invoice = FeeInvoice::findOrFail($invoiceId);
        if ($invoice->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Already fully paid'], 400);
        }

        $data = $request->validated();
        $data['fee_invoice_id'] = $invoiceId;
        $payment = FeePayment::create($data);

        $paidTotal = $invoice->getPaidAmount();
        if ($paidTotal >= $invoice->amount) {
            $invoice->update(['status' => 'paid']);
        } elseif ($paidTotal > 0) {
            $invoice->update(['status' => 'partial']);
        }

        // Mark overdue if past due
        if ($invoice->due_date->isPast() && $invoice->status !== 'paid') {
            $invoice->update(['status' => 'overdue']);
        }

        return response()->json(['success' => true, 'data' => $payment, 'message' => 'Payment recorded'], 201);
    }
}
