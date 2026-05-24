<?php

namespace App\Modules\Koperasi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CooperativeProduct extends Model
{
    protected $table = 'cooperative_products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'unit',
        'category',
        'image',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(CooperativeSale::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getIsInStockAttribute()
    {
        return $this->stock > 0;
    }
}
