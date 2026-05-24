<?php

namespace App\Modules\Finance\PaymentGateway\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Finance\Fee\Models\FeeInvoice;
use App\Modules\Finance\PaymentGateway\Models\PaymentGatewayConfig;
use App\Modules\Finance\PaymentGateway\Models\PaymentTransaction;
use App\Modules\Finance\PaymentGateway\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentGatewayWebController extends Controller
{
    public function __construct(private MidtransService $midtrans) {}

    public function index()
    {
        $transactions = PaymentTransaction::with(['invoice', 'student'])
            ->orderBy('created_at', 'desc')
            ->get();

        $configs = PaymentGatewayConfig::all();

        $totalCollected = PaymentTransaction::where('status', 'success')->sum('amount');
        $pendingCount = PaymentTransaction::where('status', 'pending')->count();

        return view('payment.index', compact('transactions', 'configs', 'totalCollected', 'pendingCount'));
    }

    public function config()
    {
        $configs = PaymentGatewayConfig::all();
        return view('payment.config', compact('configs'));
    }

    public function updateConfig(Request $request)
    {
        $data = $request->validate([
            'provider' => 'required|string',
            'merchant_id' => 'required|string',
            'server_key' => 'required|string',
            'client_key' => 'required|string',
            'is_production' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_production'] = $request->boolean('is_production');
        $data['is_active'] = $request->boolean('is_active');

        PaymentGatewayConfig::updateOrCreate(
            ['provider' => $data['provider']],
            $data
        );

        return redirect()->route('payment.config')->with('success', 'Konfigurasi berhasil disimpan');
    }

    public function payInvoice(FeeInvoice $invoice)
    {
        $trx = PaymentTransaction::create([
            'invoice_id' => $invoice->id,
            'student_id' => $invoice->student_id,
            'amount' => $invoice->getRemainingAmount(),
            'provider' => 'midtrans',
            'status' => 'pending',
        ]);

        $response = $this->midtrans->createTransaction($trx);

        return redirect()->away($response['redirect_url']);
    }

    public function callback(Request $request)
    {
        $payload = $request->all();
        $trx = $this->midtrans->handleNotification($payload);

        if ($trx && $trx->status === 'success' && $trx->invoice) {
            $trx->invoice->update(['status' => 'paid']);
        }

        return response()->json(['success' => true]);
    }
}
