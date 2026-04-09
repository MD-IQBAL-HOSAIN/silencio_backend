<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meal extends Model
{
    protected $fillable = [
        'title',
        'image',
        'description',
        'time',
        'servings',
        'calories',
        'protein',
        'carbs',
        'fats',
        'meal_type',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Attribute method for retrieving the image attribute in API requests.
     * This method is used in Laravel 10 and above.
     */
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) =>
            request()->is('api/*') && !empty($value)
                ? url($value)
                : $value
        );
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(\App\Models\Ingredient::class);
    }

    public function instructions(): HasMany
    {
        return $this->hasMany(\App\Models\Instruction::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorite_meals', 'meal_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Get all AddUserMeal records associated with the meal.
     */
    public function addUserMeals(): HasMany
    {
        return $this->hasMany(AddUserMeal::class, 'meal_id');
    }

    /**
     * Get users linked through the add_user_meals table.
     */
    public function addedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'add_user_meals', 'meal_id', 'user_id')
            ->withTimestamps();
    }
}
