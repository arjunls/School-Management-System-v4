<?php

namespace App\Modules\Budget\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetCategory extends Model
{
    protected $table = 'budget_categories';

    protected $fillable = [
        'name', 'source', 'description',
    ];

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'category_id');
    }
}
