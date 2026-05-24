<?php

namespace App\Modules\Bkk\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $fillable = [
        'vacancy_id', 'student_id', 'status', 'notes',
    ];

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
