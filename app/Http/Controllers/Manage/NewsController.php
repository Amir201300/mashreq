<?php

namespace App\Http\Controllers\Manage;

use App\Models\Category;
use App\Models\City;
use App\Models\Source;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Validator, Auth;
use App\Models\News;

class NewsController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $source = Source::all();
        $cat = Category::all();
        $City = City::all();
        return view('manage.News.index',compact('City','source','cat'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function view(Request $request)
    {
        $Sources = News::orderBY('created_at', 'desc')->get();
        return $this->dataFunction($Sources);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $News = new News();
        $News->title = $request->title;
        $News->content = $request->desc;
        $News->agel = $request->agel;
        $News->date = $request->date;
        $News->status = $request->status;
        $News->cat_id = $request->cat_id;
        $News->city_id = $request->city_id;
        $News->source_id = $request->source_id;
        if ($request->image){
        $News->image = saveImage('News',$request->image);}
        if ($request->video){
        $News->video = saveImage('News_videos',$request->video);}
        $News->save();
        return response()->json(['errors' => false]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $News = News::find($id);
        if (is_null($News)) {
            return BaseController::Error('Product not exist', 'الكلمة الدلالية غير موجودة');
        }

        return $News;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        $News = News::find($request->id);
        if (is_null($News)) {
            return response()->json(['errors' => true]);
        }
        $News->title = $request->title;
        $News->content = $request->desc;
        $News->agel = $request->agel;
        $News->date = $request->date;
        $News->status = $request->status;
        $News->cat_id = $request->cat_id;
        $News->city_id = $request->city_id;
        $News->source_id = $request->source_id;
        if ($request->image){
            deleteFile('News',$News->image);
            $News->image = saveImage('News',$request->image);
        }
        if ($request->video){
            deleteFile('News_videos',$News->video);
            $News->video = saveImage('News_videos',$request->video);
        }
        $News->save();
        return response()->json(['errors' => false]);

    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|int
     */
    public function delete(Request $request, $id)
    {
        if ($request->type == 2) {
            $ids = explode(',', $id);
            $Ads = News::whereIn('id', $ids)->delete();
        } else {
            $Ads = News::find($id);
            if (is_null($Ads)) {
                return 5;
            }
            $Ads->delete();
        }
        return response()->json(['errors' => false]);

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ChangeStatus($id,Request $request)
    {
        $News=News::find($id);
        $News->status=$request->status;
        $News->save();
        return response()->json(['errors' => false]);
    }


    private function dataFunction($data)
    {
        return Datatables::of($data)->addColumn('action', function ($data) {
            $options = '<td class="sorting_1"><button  class="btn btn-info waves-effect btn-circle waves-light" onclick="edit(' . $data->id . ')" type="button" ><i class="fa fa-spinner fa-spin" id="loadEdit_' . $data->id . '" style="display:none"></i><i class="fas fa-edit"></i></button>';
            $options .= '<button type="button" onclick="deleteFunction(' . $data->id . ',1)" class="btn btn-danger waves-effect btn-circle waves-light"><i class=" fas fa-trash"></i> </button></td>';
            return $options;
        })->addColumn('checkBox', function ($data) {
            $checkBox = '<td class="sorting_1">' .
                '<div class="custom-control custom-checkbox">' .
                '<input type="checkbox" class="mybox" id="checkBox_' . $data->id . '" onclick="check(' . $data->id . ')">' .
                '</div></td>';
            return $checkBox;
        })->editColumn('image',function($data){
            $image='<a href="/images/News/'.$data->image.'" target="_blank">'.
                '<img src="/images/News/'.$data->image.'" width="50" height="50"></a>';
            return $image;
        })->editColumn('video',function($data){
            $image='<a href="/images/News_videos/'.$data->video.'" target="_blank">'.
                '<img src="/images/News_videos/'.$data->video.'" width="50" height="50"></a>';
            return $image;
        })->editColumn('city_id',function($data){
            $city_id=$data->City ? $data->City->name : 'لايوجد';
            return $city_id;
        })->editColumn('cat_id',function($data){
            $cat_id=$data->cat ? $data->cat->name : 'لا يوجد';
            return $cat_id;
        })->editColumn('source_id',function($data){
            $source_id=$data->Source ? $data->Source->name : 'لا يوجد' ;
            return $source_id;
        })->editColumn('status',function($data){
            $status='<button class="btn waves-effect waves-light btn-rounded btn-success statusBut">'.trans('main.Active').'</button>';
            if($data->status == 0)
                $status='<button class="btn waves-effect waves-light btn-rounded btn-danger statusBut">'.trans('main.inActive').'</button>';
            return $status;
        })->rawColumns(['action' => 'action','status' => 'status', 'checkBox' => 'checkBox','icon'=>'icon','image' => 'image', 'video' => 'video','city_id' => 'city_id','cat_id' => 'cat_id','source_id' => 'source_id'])->make(true);

    }
}
