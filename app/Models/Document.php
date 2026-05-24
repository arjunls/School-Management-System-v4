<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'file_path', 'file_type', 'file_size', 'category'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
