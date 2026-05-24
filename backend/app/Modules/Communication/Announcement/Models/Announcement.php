<?php

namespace App\Modules\Communication\Announcement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'author_id', 'target_role', 'publish_at', 'expires_at'];
    protected $casts = ['publish_at' => 'datetime', 'expires_at' => 'datetime'];

    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
}
