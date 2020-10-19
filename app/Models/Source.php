<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }

    public function cat()
    {
        return $this->belongsTo(Category::class,'cat_id');
    }

    public function follow()
    {
        return $this->belongsToMany(User::class,'follow_sources','source_id','user_id');
    }
}
