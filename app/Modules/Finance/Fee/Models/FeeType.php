<?php

namespace App\Modules\Finance\Fee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeType extends Model
{
    protected $fillable = ['name', 'description', 'amount', 'frequency'];
    protected $casts = ['amount' => 'decimal:2'];

    public function invoices(): HasMany { return $this->hasMany(FeeInvoice::class); }
}
