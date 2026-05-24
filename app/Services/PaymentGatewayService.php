<?php

namespace App\Services;

use App\Modules\Finance\Fee\Models\FeeInvoice;
use App\Modules\Finance\Fee\Models\FeePayment;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentGatewayService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapToken(FeeInvoice $invoice): ?string
    {
        if (!config('midtrans.server_key')) {
            return null;
        }

        $student = $invoice->student;
        $params = [
            'transaction_details' => [
                'order_id' => 'INV-' . $invoice->id . '-' . time(),
                'gross_amount' => (int) $invoice->getRemainingAmount(),
            ],
            'customer_details' => [
                'first_name' => $student?->name ?? 'Siswa',
                'email' => $student?->email ?? '',
                'phone' => $student?->phone ?? '',
            ],
            'item_details' => [
                [
                    'id' => $invoice->feeType?->id,
                    'price' => (int) $invoice->getRemainingAmount(),
                    'quantity' => 1,
                    'name' => $invoice->feeType?->name ?? 'Pembayaran',
                ]
            ],
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            \Log::error('Midtrans snap token error: ' . $e->getMessage());
            return null;
        }
    }

    public function handleNotification(array $notification): bool
    {
        $orderId = $notification['order_id'] ?? '';
        $transactionStatus = $notification['transaction_status'] ?? '';
        $fraudStatus = $notification['fraud_status'] ?? '';

        $invoiceId = str_replace('INV-', '', explode('-', $orderId)[0]);

        $invoice = FeeInvoice::find($invoiceId);
        if (!$invoice) {
            return false;
        }

        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $this->recordSuccessfulPayment($invoice, $notification);
        } elseif ($transactionStatus === 'settlement') {
            $this->recordSuccessfulPayment($invoice, $notification);
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $invoice->update(['status' => 'unpaid']);
        }

        return true;
    }

    private function recordSuccessfulPayment(FeeInvoice $invoice, array $notification): void
    {
        FeePayment::create([
            'fee_invoice_id' => $invoice->id,
            'amount' => $notification['gross_amount'] ?? $invoice->getRemainingAmount(),
            'payment_date' => now(),
            'payment_method' => 'transfer',
            'reference_no' => $notification['transaction_id'] ?? null,
            'notes' => 'Midtrans: ' . ($notification['payment_type'] ?? ''),
        ]);

        $totalPaid = $invoice->getPaidAmount();
        if ($totalPaid >= $invoice->amount) {
            $invoice->update(['status' => 'paid']);
        } else {
            $invoice->update(['status' => 'partial']);
        }
    }
}
