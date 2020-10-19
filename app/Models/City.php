<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public function news()
    {
        return $this->hasMany(News::class,'city_id');
    }
}
