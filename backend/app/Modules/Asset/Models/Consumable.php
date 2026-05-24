<?php

namespace App\Modules\Asset\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    use HasFactory;

    protected $table = 'consumables';

    protected $fillable = [
        'name',
        'unit',
        'stock',
        'min_stock',
        'category',
    ];

    protected $casts = [
        'stock' => 'integer',
        'min_stock' => 'integer',
    ];

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) return 'out';
        if ($this->stock <= $this->min_stock) return 'low';
        return 'sufficient';
    }
}
