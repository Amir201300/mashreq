<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
    $date= \Carbon\Carbon::setLocale('ar');
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'agel' => (int)$this->agel,
            'view' => (int)$this->view,
            'city' => $this->city ? $this->city->name : null,
            'category' => $this->cat ? $this->cat->name : null,
            'source' => $this->source ? $this->source->name : null,
            'source_logo' => $this->source ? getImageUrl('Source',$this->source->logo) : null,
            'image' => getImageUrl('News',$this->image),
            'video' => $this->video ? getImageUrl('News',$this->video) : null,
            'city_id' => $this->city_id,
            'source_id' => $this->source_id,
            'category_id' => $this->cat_id,
            'date' => $this->date,
            'created_at'=>$this->created_at->diffForHumans()

        ];
    }
}
