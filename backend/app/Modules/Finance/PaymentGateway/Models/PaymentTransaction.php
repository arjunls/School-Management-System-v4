<?php

namespace App\Modules\Finance\PaymentGateway\Models;

use App\Models\User;
use App\Modules\Finance\Fee\Models\FeeInvoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';

    protected $fillable = ['invoice_id', 'student_id', 'amount', 'provider', 'transaction_id', 'status', 'payment_method', 'paid_at', 'raw_response'];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'raw_response' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(FeeInvoice::class, 'invoice_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
