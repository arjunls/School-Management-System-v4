<?php

namespace App\Modules\Finance\PaymentGateway\Services;

use App\Modules\Finance\PaymentGateway\Models\PaymentGatewayConfig;
use App\Modules\Finance\PaymentGateway\Models\PaymentTransaction;

class MidtransService
{
    protected ?PaymentGatewayConfig $config;

    public function __construct()
    {
        $this->config = PaymentGatewayConfig::where('provider', 'midtrans')
            ->where('is_active', true)
            ->first();
    }

    public function createTransaction(PaymentTransaction $trx): array
    {
        $orderId = 'INV-' . $trx->id . '-' . time();

        $trx->update(['transaction_id' => $orderId]);

        return [
            'success' => true,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $orderId,
            'transaction_id' => $orderId,
            'token' => 'mock-token-' . $trx->id,
            'gross_amount' => $trx->amount,
        ];
    }

    public function handleNotification(array $payload): PaymentTransaction|null
    {
        $transactionId = $payload['transaction_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$transactionId) {
            return null;
        }

        $trx = PaymentTransaction::where('transaction_id', $transactionId)->first();
        if (!$trx) {
            return null;
        }

        $statusMap = [
            'capture' => 'success',
            'settlement' => 'success',
            'pending' => 'pending',
            'deny' => 'failed',
            'cancel' => 'failed',
            'expire' => 'expired',
            'failure' => 'failed',
        ];

        $status = $statusMap[$transactionStatus] ?? 'pending';

        if ($fraudStatus === 'deny') {
            $status = 'failed';
        }

        $trx->update([
            'status' => $status,
            'payment_method' => $payload['payment_type'] ?? null,
            'paid_at' => $status === 'success' ? now() : null,
            'raw_response' => $payload,
        ]);

        return $trx;
    }

    public function checkStatus(string $transactionId): array
    {
        $trx = PaymentTransaction::where('transaction_id', $transactionId)->first();

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'transaction_status' => $trx?->status ?? 'not_found',
            'gross_amount' => $trx?->amount ?? 0,
            'payment_type' => $trx?->payment_method ?? null,
            'transaction_time' => $trx?->created_at?->toIso8601String(),
        ];
    }
}
