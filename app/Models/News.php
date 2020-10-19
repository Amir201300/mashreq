<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }

    public function cat()
    {
        return $this->belongsTo(Category::class,'cat_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class,'source_id');
    }
}
