<?php

namespace App\Http\Resources;

use App\Models\FollowSource;
use Illuminate\Http\Resources\Json\JsonResource;


class SourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $is_follow =false;
        $user=$user=auth('api')->user();
        if($user){
            $follow=FollowSource::where('user_id',$user->id)->where('source_id',$this->id)->first();
            $is_follow=!is_null($follow) ? true : false;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'desc' => $this->desc,
            'city' => $this->city ? $this->city->name : null,
            'category' => $this->cat ? $this->cat->name : null,
            'logo' => getImageUrl('Source',$this->logo),
            'cover_photo' => getImageUrl('Source',$this->cover_photo),
            'city_id' => (int)$this->city_id,
            'cat_id' =>(int) $this->cat_id,
            'number_of_follow' => $this->follow ? $this->follow->count() :0 ,
            'is_follow'=>$is_follow
        ];
    }
}
