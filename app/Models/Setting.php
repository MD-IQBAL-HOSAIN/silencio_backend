<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = ['id'];

    /**
     * Attribute method for retrieving the logo attribute in API requests.
     * This method is used in Laravel 10 and above.
     */
    protected function logo(): Attribute
    {
        return Attribute::make(
            get: fn($value) =>
            request()->is('api/*') && !empty($value)
                ? url($value)
                : $value
        );
    }
}
