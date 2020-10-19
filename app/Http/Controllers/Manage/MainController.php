<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Auth;

class MainController extends Controller
{

    public function index()
    {
    	return view('manage.dashboard');
    }


   public function login(Request $request){

    $credentials = [

        'name' => $request['name'],

        'password' => $request['password'],

    ];

    if (Auth::guard('Admin')->attempt($credentials)) {


      if(Auth::guard('Admin')->user()->hasRole('Admin')){
        return 1;

        }

        return 1;
        }

    return 2;

   }

	public function logout(){
		Auth::guard('Admin')->logout();
		return redirect('/manage/login');

	}
}
