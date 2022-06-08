<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = ['name', 'address', 'website', 'email', 'phones', 'product_categories'];


    protected $casts = [
        'phones' => 'array',
        'product_categories' => 'array',
    ];
}
