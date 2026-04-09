<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddUserMeal extends Model
{
    /**
     * Table associated with this model.
     *
     * @var string
     */
    protected $table = 'add_user_meals';

    /**
     * Attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'meal_id',
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
     * Get the user that owns this link.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the meal that belongs to this link.
     */
    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }

}
