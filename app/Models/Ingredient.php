<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends Model
{
    protected $fillable = [
        'meal_id',
        'grocery_type_id',
        'title',
        'quantity',
    ];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }

    public function groceryType(): BelongsTo
    {
        return $this->belongsTo(GroceryType::class);
    }
}
