<?php

namespace App\Modules\Security\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TwoFactorAuth extends Model
{
    protected $table = 'two_factor_auth';

    protected $fillable = ['user_id', 'secret', 'is_enabled', 'backup_codes', 'last_used_at'];

    protected $casts = [
        'is_enabled' => 'boolean',
        'backup_codes' => 'array',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}