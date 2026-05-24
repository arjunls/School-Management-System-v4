<?php

namespace App\Modules\StudentLife\Career\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CareerInterest extends Model
{
    protected $table = 'career_interests';

    protected $fillable = [
        'student_id', 'code', 'label', 'score', 'test_date', 'notes',
    ];

    protected $casts = [
        'test_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
