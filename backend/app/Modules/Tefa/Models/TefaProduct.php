<?php

namespace App\Modules\Tefa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TefaProduct extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'stock', 'unit', 'category', 'image', 'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function productions(): HasMany
    {
        return $this->hasMany(TefaProduction::class, 'product_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(TefaSale::class, 'product_id');
    }
}
