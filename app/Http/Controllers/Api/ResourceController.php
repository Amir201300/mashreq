<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CityResource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\SourceResource;
use App\Models\City;
use App\Models\FollowSource;
use App\Models\News;
use App\Models\Source;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator,Auth,Artisan,Hash,File,Crypt;

class ResourceController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_resources()
    {
        $sources=Source::where('id','!=',1)->get();
        return $this->apiResponseData(SourceResource::collection($sources),'',200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function masreqNews()
    {
        $news=News::where('source_id',1)->get();
        return $this->apiResponseData(NewsResource::collection($news),'',200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function add_resource(Request $request)
    {
        $user=Auth::user();
        $source=Source::find($request->source_id);
        if(is_null($source)){
            return $this->apiResponseMessage(0,'المصدر غير موجود',200);
        }
        $followResouce=FollowSource::where('user_id',$user->id)->where('source_id',$request->source_id)->first();
        if(is_null($followResouce)){
            $followResouce=new FollowSource();
            $followResouce->user_id=$user->id;
            $followResouce->source_id=$request->source_id;
            $followResouce->save();
            $msg='تم متابعه المصدر بنجاح';
        }else{
            $followResouce->delete();
            $msg='تم الغاء المتابعه';
        }
        //return $source->follow->count();
        return $this->apiResponseData(SourceResource::collection($user->my_source),$msg,200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_my_resource()
    {
        $user=Auth::user();
        return $this->apiResponseData(SourceResource::collection($user->my_source),'تمت العملية بنجاح',200);

    }


}
