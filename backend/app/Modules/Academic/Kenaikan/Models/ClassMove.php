<?php

namespace App\Modules\Academic\Kenaikan\Models;

use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassMove extends Model
{
    protected $table = 'class_moves';

    protected $fillable = [
        'student_id', 'from_class_id', 'to_class_id', 'academic_year',
        'reason', 'is_graduated', 'status', 'approved_by',
    ];

    protected $casts = [
        'is_graduated' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function fromClass(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'from_class_id');
    }

    public function toClass(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'to_class_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
