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
        return $this->success(FeeType::orderBy('name')->get());
    }

    /**
     * Create a new fee type
     */
    public function typeStore(StoreFeeTypeRequest $request)
    {
        $type = FeeType::create($request->validated());
        return $this->created($type, 'Fee type created');
    }

    /**
     * Update a fee type
     */
    public function typeUpdate(UpdateFeeTypeRequest $request, int $id)
    {
        $type = FeeType::findOrFail($id);
        $type->update($request->validated());
        return $this->success($type, 'Fee type updated');
    }

    /**
     * Delete a fee type
     */
    public function typeDelete(int $id)
    {
        FeeType::findOrFail($id)->delete();
        return $this->deleted('Fee type deleted');
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

        return $this->paginated($query->orderByDesc('created_at')->paginate($request->per_page ?? 20));
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
        return $this->created($invoice, 'Invoice created');
    }

    // Payments
    /**
     * Record a payment for an invoice
     */
    public function pay(PayInvoiceRequest $request, int $invoiceId)
    {
        $invoice = FeeInvoice::findOrFail($invoiceId);
        if ($invoice->status === 'paid') {
            return $this->error('Already fully paid', 400);
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

        return $this->created($payment, 'Payment recorded');
    }
}
