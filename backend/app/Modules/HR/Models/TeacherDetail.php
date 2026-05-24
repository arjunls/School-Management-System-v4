<?php

namespace App\Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherDetail extends Model
{
    use HasFactory;

    protected $table = 'teacher_details';

    protected $fillable = [
        'user_id',
        'nip',
        'nuptk',
        'certification',
        'education',
        'education_institution',
        'graduation_year',
        'address',
        'birth_place',
        'birth_date',
        'religion',
        'marital_status',
        'employment_status',
        'join_date',
        'subject_specialization',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'join_date' => 'date',
        'graduation_year' => 'integer',
        'certification' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
