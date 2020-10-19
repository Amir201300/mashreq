<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Validator, Auth;
use App\Models\Service;

class ServiceController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('manage.Service.index');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function view(Request $request)
    {
        $Service = Service::orderBY('created_at', 'desc')->get();
        return $this->dataFunction($Service);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {


        $Service = new Service();
        $Service->name_ar = $request->name_ar;
        if($request->icon)
            $Service->icon = saveImage('Service',$request->icon);
        $Service->name_en = $request->name_en;
        $Service->save();
        return response()->json(['errors' => false]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $Service = Service::find($id);
        if (is_null($Service)) {
            return BaseController::Error('Product not exist', 'الكلمة الدلالية غير موجودة');
        }

        return $Service;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        $Service = Service::find($request->id);
        if (is_null($Service)) {
            return response()->json(['errors' => true]);
        }
        $Service->name_ar = $request->name_ar;
        if($request->icon) {
            deleteFile('Service',$Service->icon);
            $Service->icon = saveImage('Service', $request->icon);
        }
        $Service->name_en = $request->name_en;
        $Service->save();
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
            $Ads = Service::whereIn('id', $ids)->delete();
        } else {
            $Ads = Service::find($id);
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
        $Service=Service::find($id);
        $Service->status=$request->status;
        $Service->save();
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
        })->editColumn('icon',function($data){
            $image='<a href="'.getImageUrl('Service',$data->icon).'" target="_blank">'.
                '<img src="'.getImageUrl('Service',$data->icon).'" width="50" height="50"></a>';
            return $image;
        })->rawColumns(['action' => 'action', 'checkBox' => 'checkBox','icon'=>'icon'])->make(true);

    }
}
