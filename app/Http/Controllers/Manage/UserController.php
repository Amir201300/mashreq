<?php

namespace App\Http\Controllers\Manage;

use App\Models\Category;
use App\Models\City;
use App\Models\Source;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Validator, Auth;
use App\User;

class UserController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        return view('manage.User.index');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function view(Request $request)
    {
        $user = User::orderBY('created_at', 'desc')->get();
        return $this->dataFunction($user);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return BaseController::Error('Product not exist', 'الكلمة الدلالية غير موجودة');
        }

        return $user;
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
            $Ads = User::whereIn('id', $ids)->delete();
        } else {
            $Ads = User::find($id);
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
        $user=User::find($id);
        $user->status=$request->status;
        $user->save();
        return response()->json(['errors' => false]);
    }


    private function dataFunction($data)
    {
        return Datatables::of($data)->addColumn('action', function ($data) {
            $options = '<button type="button" onclick="deleteFunction(' . $data->id . ',1)" class="btn btn-danger waves-effect btn-circle waves-light"><i class=" fas fa-trash"></i> </button></td>';
            return $options;
        })->addColumn('checkBox', function ($data) {
            $checkBox = '<td class="sorting_1">' .
                '<div class="custom-control custom-checkbox">' .
                '<input type="checkbox" class="mybox" id="checkBox_' . $data->id . '" onclick="check(' . $data->id . ')">' .
                '</div></td>';
            return $checkBox;
        })->editColumn('status',function($data){
            $status='<button class="btn waves-effect waves-light btn-rounded btn-success statusBut">'.trans('main.Active').'</button>';
            if($data->status == 0)
                $status='<button class="btn waves-effect waves-light btn-rounded btn-danger statusBut">'.trans('main.inActive').'</button>';
            return $status;
        })->editColumn('image',function($data){
            $image='<a href="/images/User/'.$data->image.'" target="_blank">'.
                '<img src="/images/User/'.$data->image.'" width="50" height="50"></a>';
            return $image;
        })->rawColumns(['action' => 'action','status' => 'status', 'checkBox' => 'checkBox','icon'=>'icon','image'=>'image'])->make(true);

    }
}
