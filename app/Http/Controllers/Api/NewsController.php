<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CityResource;
use App\Http\Resources\NewsResource;
use App\Models\City;
use App\Models\News;
use App\Models\Source;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator,Auth,Artisan,Hash,File,Crypt;

class NewsController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function home()
    {
        $city=City::whereHas('news')->take(5)->get();
        $latest_news=News::orderBy('id','desc')->take(5)->get();
        $agel_news=News::orderBy('id','desc')->where('agel',1)->take(5)->get();
        $most_read=News::orderBy('view','desc')->take(5)->get();
        $data=['latest_news'=>NewsResource::collection($latest_news),
            'most_read'=>NewsResource::collection($most_read),
            'agel_news'=>NewsResource::collection($agel_news),
            'cities'=>CityResource::collection($city)
        ];
        return $this->apiResponseData($data,'تت العملية بنجاح',200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function news_by_city(Request $request)
    {
        $news=News::where('city_id',$request->city_id)->get();
        return $this->apiResponseData(NewsResource::collection($news),'تت العملية بنجاح',200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function news_by_source(Request $request)
    {
        $news=News::where('source_id',$request->source_id)->get();
        return $this->apiResponseData(NewsResource::collection($news),'تت العملية بنجاح',200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function news_by_cat(Request $request)
    {
        $news=News::where('cat_id',$request->cat_id)->get();
        return $this->apiResponseData(NewsResource::collection($news),'تت العملية بنجاح',200);
    }

    /**
     * @param $new_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function single_new($new_id)
    {
        $new=News::find($new_id);
        if(is_null($new))
        {
        return $this->apiResponseMessage(0, 'الخبر غير موجود', 200);

        }
        return $this->apiResponseData(new NewsResource($new),'تت العملية بنجاح',200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter_news(Request $request){
        $news=new News;
        if($request->filter == 1)
            $news=$news->orderBy('id','desc');
        if($request->type == 2)
            $news=$news->where('video','!=',null);
        $news=$news->get();
        return $this->apiResponseData(NewsResource::collection($news),'تمت العملية بنجاح',200);
    }

}
