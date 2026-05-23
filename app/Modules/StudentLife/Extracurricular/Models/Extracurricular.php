<?php

namespace App\Modules\StudentLife\Extracurricular\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Extracurricular extends Model
{
    protected $fillable = ['name', 'description', 'coach', 'day', 'start_time', 'end_time', 'location', 'max_participants'];
    protected $casts = ['start_time' => 'datetime:H:i', 'end_time' => 'datetime:H:i'];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'extracurricular_participants', 'extracurricular_id', 'student_id')
            ->withPivot(['joined_at', 'status'])->withTimestamps();
    }

    public function activeParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivot('status', 'active');
    }
}
