<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Validator, Auth;
use App\Models\Category;

class CategoryController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('manage.Category.index');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function view(Request $request)
    {
        $Category = Category::orderBY('created_at', 'desc')->get();
        return $this->dataFunction($Category);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {


        $Category = new Category();
        $Category->name = $request->name;
        $Category->type = $request->type;
        $Category->save();
        return response()->json(['errors' => false]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $Category = Category::find($id);
        if (is_null($Category)) {
            return BaseController::Error('Product not exist', 'الكلمة الدلالية غير موجودة');
        }

        return $Category;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        $Category = Category::find($request->id);
        if (is_null($Category)) {
            return response()->json(['errors' => true]);
        }
        $Category->name = $request->name;
        $Category->type = $request->type;
        $Category->save();
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
            $Ads = Category::whereIn('id', $ids)->delete();
        } else {
            $Ads = Category::find($id);
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
        $Category=Category::find($id);
        $Category->status=$request->status;
        $Category->save();
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
        })->editColumn('type',function($data){
            if($data->type == 0){
            $status='<button class="btn waves-effect waves-light btn-rounded btn-success statusBut">'.trans('main.News').'</button>';
            }else{
                $status='<button class="btn waves-effect waves-light btn-rounded btn-danger statusBut">'.trans('main.Live').'</button>';
            }
                return $status;
        })->rawColumns(['action' => 'action', 'checkBox' => 'checkBox','icon'=>'icon','type' => 'type'])->make(true);

    }
}
