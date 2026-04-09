<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'page_title',
        'page_content',
        'status',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
