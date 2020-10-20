<?php

namespace App\Http\Controllers\Manage;

use App\Models\Category;
use App\Models\City;
use App\Models\Source;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Validator, Auth;
use App\Models\Live;

class LiveController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $cat = Category::where('type',1);
        return view('manage.Live.index',compact('cat'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function view(Request $request)
    {
        $live = Live::orderBY('created_at', 'desc')->get();
        return $this->dataFunction($live);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $live = new Live();
        $live->name = $request->name;
        $live->link = $request->link;
        $live->cat_id = $request->cat_id;
        if ($request->image){
            $live->image = saveImage('Live',$request->image);}
        $live->save();
        return response()->json(['errors' => false]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $live = Live::find($id);
        if (is_null($live)) {
            return BaseController::Error('Product not exist', 'الكلمة الدلالية غير موجودة');
        }

        return $live;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        $live = Live::find($request->id);
        if (is_null($live)) {
            return response()->json(['errors' => true]);
        }
        $live->name = $request->name;
        $live->link = $request->link;
        $live->cat_id = $request->cat_id;
        if ($request->image){
            deleteFile('Live',$live->image);
            $live->image = saveImage('Live',$request->image);
        }
        $live->save();
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
            $Ads = Live::whereIn('id', $ids)->delete();
        } else {
            $Ads = Live::find($id);
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
        $live=Live::find($id);
        $live->status=$request->status;
        $live->save();
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
            $image='<a href="/images/Live/'.$data->image.'" target="_blank">'.
                '<img src="/images/Live/'.$data->image.'" width="50" height="50"></a>';
            return $image;
        })->editColumn('cat_id',function($data){
            $cat_id=$data->cat ? $data->cat->name : 'لا يوجد';
            return $cat_id;
        })->rawColumns(['action' => 'action', 'checkBox' => 'checkBox','icon'=>'icon','cat_id' => 'cat_id'])->make(true);

    }
}
