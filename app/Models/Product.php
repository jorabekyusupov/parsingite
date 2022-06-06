<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'url', 'categories', 'seller_id'];

    protected $casts = [
        'categories' => 'array',
    ];
}
