<?php

namespace App\Modules\Website\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'title', 'description', 'image_path', 'category',
    ];
}
