<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    public $timestamps = false;

    public function orders()
    {
        return $this->hasMany('App\Models\Orders');
    }

    public function getPriceAttribute($price)
    {
        return number_format($price, 2);
    }
}
