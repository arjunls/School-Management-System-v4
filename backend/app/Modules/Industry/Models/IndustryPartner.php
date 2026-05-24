<?php

namespace App\Modules\Industry\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndustryPartner extends Model
{
    protected $fillable = [
        'name', 'address', 'phone', 'email', 'pic_name', 'pic_phone',
        'cooperation_type', 'start_date', 'end_date', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function programs(): HasMany
    {
        return $this->hasMany(IndustryProgram::class, 'partner_id');
    }
}
