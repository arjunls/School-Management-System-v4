<?php

namespace App\Modules\Fee\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeInvoice extends Model
{
    protected $fillable = ['fee_type_id', 'student_id', 'amount', 'due_date', 'status', 'notes'];
    protected $casts = ['amount' => 'decimal:2', 'due_date' => 'date'];

    public function feeType(): BelongsTo { return $this->belongsTo(FeeType::class); }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }
    public function payments(): HasMany { return $this->hasMany(FeePayment::class); }

    public function getPaidAmount(): float { return $this->payments()->sum('amount'); }
    public function getRemainingAmount(): float { return $this->amount - $this->getPaidAmount(); }
}
