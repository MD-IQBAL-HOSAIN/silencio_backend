<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Instruction extends Model
{
    protected $fillable = [
        'meal_id',
        'title',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
