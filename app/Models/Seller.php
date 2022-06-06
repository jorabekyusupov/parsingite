<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = ['name', 'address', 'website', 'email', 'phones'];


    protected $casts = [
        'phones' => 'array',
    ];
}
