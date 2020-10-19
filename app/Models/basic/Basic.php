<?php
namespace App\Models\basic;
use App\Models\Shop;

use Auth;



class Basic {

    public static function getImage()

    {

        return '/manage/assets/images/users/2.jpg';

    }

    public static function getNumbers()
    {
        $all_shop=Shop::count();
        $shop_premium=Shop::where('type',1)->count();
        $shop_free=Shop::where('type',0)->count();
        $order_premium=Shop::where('status',3)->count();
        return ['all_shop'=>$all_shop,'shop_premium'=>$shop_premium,'shop_free'=>$shop_free,'order_premium'=>$order_premium];

    }

    public static function get_last_shop()
    {
      
        $shops=Shop::orderBy('created_at','desc')->take(5)->get();
        return $shops;
    }

    public static function get_status($status)
    {
        $class='danger';
        $name=trans('main.inActive');

        if($status == 3){
            $class='warning';
            $name=trans('main.pinding_premium');
        }elseif($status==2){
            $class='info';
            $name=trans('main.pinding_approvel');

        }elseif($status==1){
            $class='success';
            $name=trans('main.Active');

        }
        return ['class'=>$class,'name'=>$name];
    }

}

