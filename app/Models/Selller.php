<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Selller extends Model
{
    protected $table = 'selllers';


    public function product()
    {
        return $this->hasOne(Product::class, 'seller_id', 'id');
    }
}
