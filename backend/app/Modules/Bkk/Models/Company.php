<?php

namespace App\Modules\Bkk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name', 'field', 'address', 'phone', 'email', 'website',
        'contact_person', 'logo', 'mou_date', 'mou_expiry', 'status',
    ];

    protected $casts = [
        'mou_date' => 'date',
        'mou_expiry' => 'date',
    ];

    public function vacancies(): HasMany
    {
        return $this->hasMany(JobVacancy::class);
    }
}
