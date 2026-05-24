<?php

namespace App\Modules\Asrama\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DormitoryAssignment extends Model
{
    protected $fillable = [
        'room_id', 'student_id', 'check_in_date', 'check_out_date', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(DormitoryRoom::class, 'room_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'student_id');
    }
}
