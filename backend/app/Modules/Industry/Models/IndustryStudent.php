<?php

namespace App\Modules\Industry\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndustryStudent extends Model
{
    protected $fillable = [
        'program_id', 'student_id', 'mentor_id', 'status',
        'start_date', 'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(IndustryProgram::class, 'program_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
