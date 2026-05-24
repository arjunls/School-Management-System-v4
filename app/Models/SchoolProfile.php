<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    protected $fillable = [
        'name', 'address', 'phone', 'email', 'website',
        'logo', 'npsn', 'kepala_sekolah', 'akreditasi', 'description',
    ];
}
