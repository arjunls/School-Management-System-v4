<?php

namespace App\Modules\StudentManagement\Health\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthRecord extends Model
{
    protected $fillable = ['student_id', 'blood_type', 'allergies', 'medical_conditions', 'medications', 'emergency_contact_name', 'emergency_contact_phone', 'notes'];

    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }
}
