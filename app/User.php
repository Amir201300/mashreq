<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;


class User extends Authenticatable
{


    use HasApiTokens, Notifiable;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function my_source()
    {
        return $this->belongsToMany(User::class,'follow_sources','user_id','source_id');

    }

    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function my_wishlist()
    {
        return $this->belongsToMany('App\Models\Products','whishlists','user_id','product_id');
    }

    public function my_cart()
    {
        return $this->belongsToMany('App\Models\Products','carts','user_id','product_id')
            ->withPivot('color_id','size_id','quantity');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order','user_id');
    }

}
