<?php

namespace App\Modules\StudentLife\Career\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CareerPlan extends Model
{
    protected $table = 'career_plans';

    protected $fillable = [
        'student_id', 'plan_type', 'institution', 'major', 'goal', 'notes',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
