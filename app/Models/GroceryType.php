<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroceryType extends Model
{
    protected $fillable = [
        'name',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }
}
