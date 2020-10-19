<?php

namespace App\Http\Controllers\Manage;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Validator, Auth;
use App\Models\Source;

class SourcesController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $City = City::all();
        return view('manage.Sources.index',compact('City'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function view(Request $request)
    {
        $Sources = Source::orderBY('created_at', 'desc')->get();
        return $this->dataFunction($Sources);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $this->validate(
            $request,
            [
                'name' => 'required',
            ]
        );
        $Sources = new Source();
        $Sources->name = $request->name;
        $Sources->desc = $request->desc;
        $Sources->city_id = $request->city_id;
        $Sources->logo = saveImage('Sources',$request->logo);
        $Sources->cover_photo = saveImage('Sources_cover',$request->cover_photo);
        $Sources->save();
        return response()->json(['errors' => false]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $Sources = Source::find($id);
        if (is_null($Sources)) {
            return BaseController::Error('Product not exist', 'الكلمة الدلالية غير موجودة');
        }

        return $Sources;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        $Sources = Source::find($request->id);
        if (is_null($Sources)) {
            return response()->json(['errors' => true]);
        }
        $Sources->name = $request->name;
        $Sources->desc = $request->desc;
        $Sources->city_id = $request->city_id;
        if ($request->logo){
            deleteFile('City',$Sources->logo);
            $Sources->logo = saveImage('Sources',$request->logo);
        }
        if ($request->cover_photo){
            deleteFile('City',$Sources->cover_photo);
            $Sources->cover_photo = saveImage('Sources_cover',$request->cover_photo);
        }
        $Sources->save();
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
            $Ads = Source::whereIn('id', $ids)->delete();
        } else {
            $Ads = Source::find($id);
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
        $Sources=Source::find($id);
        $Sources->status=$request->status;
        $Sources->save();
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
        })->editColumn('logo',function($data){
            $image='<a href="/images/Sources/'.$data->logo.'" target="_blank">'.
                '<img src="/images/Sources/'.$data->logo.'" width="50" height="50"></a>';
            return $image;
        })->editColumn('cover_photo',function($data){
            $image='<a href="/images/Sources_cover/'.$data->cover_photo.'" target="_blank">'.
                '<img src="/images/Sources_cover/'.$data->cover_photo.'" width="50" height="50"></a>';
            return $image;
//        })->editColumn('city_id',function($data){
//            $city_id=$data->City->name;
//            return $city_id;
        })->rawColumns(['action' => 'action', 'checkBox' => 'checkBox','icon'=>'icon','logo' => 'logo', 'cover_photo' => 'cover_photo','city_id' => 'city_id'])->make(true);

    }
}
