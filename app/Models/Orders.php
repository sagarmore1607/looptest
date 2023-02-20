<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    public $timestamps = false;

    public function customers()
    {
        return $this->belongsTo('App\Models\Customer','customer')->select('id','fname_lname');
    }

    public function products()
    {
        return $this->belongsTo('App\Models\Products','product_id')->select('id','product_name','price');
    }
}
