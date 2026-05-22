<?php

namespace App\Modules\Fee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePayment extends Model
{
    protected $fillable = ['fee_invoice_id', 'amount', 'payment_date', 'payment_method', 'reference_no', 'notes'];
    protected $casts = ['amount' => 'decimal:2', 'payment_date' => 'date'];

    public function invoice(): BelongsTo { return $this->belongsTo(FeeInvoice::class, 'fee_invoice_id'); }
}
