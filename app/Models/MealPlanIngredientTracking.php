<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealPlanIngredientTracking extends Model
{
    /**
     * Attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'meal_plan_id',
        'plan_date',
        'ingredient_id',
        'is_purchased',
    ];

    /**
     * Attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'plan_date' => 'date',
        'is_purchased' => 'boolean',
    ];

    /**
     * Get owner user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get linked ingredient when available.
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
