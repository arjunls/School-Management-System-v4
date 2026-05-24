<?php

namespace App\Modules\Security\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    protected $table = 'security_logs';

    protected $fillable = ['user_id', 'event', 'ip_address', 'user_agent', 'details'];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
