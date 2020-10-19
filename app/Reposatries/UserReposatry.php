<?php

namespace App\Reposatries;

use App\Http\Controllers\Manage\EmailsController;
use App\Http\Resources\UserResource;
use App\Interfaces\UserInterface;
use App\User;
use Illuminate\Http\Request;
use Validator,Auth,Artisan,Hash,File,Crypt;

class UserReposatry implements UserInterface {
    use \App\Traits\ApiResponseTrait;

    /**
     * @param $request
     * @param $user
     * @param $type
     * @return mixed
     */
    public function save_user($request,$user,$type)
    {
        $code=mt_rand(999,9999);
        $user->email = $request->email;
        $user->name = $request->name;
        if($type ==1) {
            $user->status = 0;
            $user->code_active=$code;
        }
        if($request->password)
            $user->password = Hash::make($request->password);
        if($request->image) {
            deleteFile('users',$user->image);
            $user->image = saveImage('users', $request->file('image'));
        }
        $user->save();
        $token = $user->createToken('TutsForWeb')->accessToken;
        if($type ==1) {
            $user['token_user'] = $token;
            //EmailsController::verify_email($user->id,$lang);
        }
        return $user;
    }

    /***
     * @param $request
     * @param $user_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function validate_user($request, $user_id)
    {
        $lang = $request->header('lang');
        $input = $request->all();
        $validationMessages = [
            'name.required' => $lang == 'en' ?  'name is required ' :" من فضلك ادخل الاسم" ,
            'password.required' => $lang == 'en' ? 'password is required'  : 'من فضلك ادخل كلمة السر' ,
            'email.required' => $lang == 'en' ?  "email is required"  :'من فضلك ادخل البريد الالكتروني' ,
            'email.unique' => $lang == 'en' ?  "email is already teken" : 'هذا البريد الالكتروني موجود لدينا بالفعل' ,
            'email.regex'=>$lang=='en'?  'The email must be a valid email address' : 'من فضلك ادخل بريد الكتروني صالح',
        ];

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => $user_id ==0 ? 'required|unique:users|regex:/(.+)@(.+)\.(.+)/i' : 'required|unique:users,email,'.$user_id.'|regex:/(.+)@(.+)\.(.+)/i',
            'password' =>  $user_id == 0 ? 'required': '' ,
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 2500);
        }
    }


    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|mixed
     */
    public function login($request)
    {
        //return $request->emailOrphone;
        $lang = $request->header('lang');
        $user=User::where('email',$request->email);
        $user=$user->first();
        if(is_null($user))
        {
            $msg=$lang=='en' ? 'user does not exist' : 'البيانات المدخلة غير موجودة لدينا ' ;
            return $this->apiResponseMessage( 0,$msg, 200);
        }
        $password = Hash::check($request->password, $user->password);
        if ($password == false) {
            $msg = $lang == 'en' ? 'Password is not correct' :'كلمة السر غير صحيحة' ;
            return $this->apiResponseMessage(0, $msg, 200);
        }
        $token = $user->createToken('TutsForWeb')->accessToken;
        $user['token_user']=$token;
        $msg=$lang=='en' ? 'login success' : 'تم تسجيل الدخول بنجاح';
        return $this->apiResponseData(new UserResource($user),$msg,200);
    }


}
