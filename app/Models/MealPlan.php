<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlan extends Model
{
    /**
     * Attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'set_calories',
        'plan_date',
    ];


    /**
     * Attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'plan_date' => 'date',
    ];

    /**
     * Get the user that owns this meal plan entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all dynamic plan items for this day.
     */
    public function items(): HasMany
    {
        return $this->hasMany(\App\Models\MealPlanItem::class, 'meal_plan_id')->orderBy('id');
    }
}
