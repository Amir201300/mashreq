<?php

namespace App\Http\Controllers\Api;

use App\Interfaces\UserInterface;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator,Auth,Artisan,Hash,File,Crypt;
use App\Http\Resources\UserResource;
use App\User;
use App\Http\Controllers\Manage\EmailsController;

class UserController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param Request $request
     * @param UserInterface $userFunction
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function register(Request $request,UserInterface $userFunction)
    {
        $lang = $request->header('lang');
        $validate_user=$userFunction->validate_user($request,0);
        if(isset($validate_user)){
            return  $validate_user;
        }
        $user=$userFunction->save_user($request,new User(),1);
        EmailsController::verify_email($user->id,'ar');
        $msg=$lang == 'en' ? 'تم التسجيل بنجاح' : 'تم إرسال كود التفعيل الى بريدك الالكترونى';
        return $this->apiResponseData(new UserResource($user),$msg,200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function check_activation_code(Request $request)
    {
        $user=Auth::user();
        if($request->code != $user->code_active)
        {
            $msg='الكود غير صحيح' ;
            return $this->apiResponseMessage(0,$msg,200);
        }
        $user->code_active=null;
        $user->status=1;
        $user->save();
        $msg= 'تم التفعيل بنجاح' ;
        return $this->apiResponseMessage(1,$msg,200);

    }



    /**
     * @param Request $request
     * @param UserInterface $user
     * @return mixed
     */
    public function login(Request $request,UserInterface $user)
    {
        return $user->login($request);
    }

    /*
     * Change Password
     * @pram old passsword , newpassword
     */
    public function change_password(Request $request)
    {
        $user = Auth::user();
        if(!$request->newPassword){
            $msg= 'يجب ادخال كلمة السر الجديدة' ;
            return $this->apiResponseMessage(0,$msg,200);
        }
        $password=Hash::check($request->oldPassword,$user->password);
        if($password==true){
            $user->password=Hash::make($request->newPassword);
            $user->save();
            $msg='تم تغيير كلمة السر بنجاح';
            return $this->apiResponseMessage( 1,$msg, 200);

        }else{
            $msg='كلمة السر القديمة غير صحيحة' ;
            return $this->apiResponseMessage(0,$msg, 200);

        }
    }
    /*
     * Edit user
     * @pram old passsword , newpassword
    */


    public function edit_profile(Request $request,UserInterface  $userFunction)
    {
        $lang = $request->header('lang');
        $user=Auth::user();
        $validate_user=$userFunction->validate_user($request,$user->id);
        if(isset($validate_user)){
            return  $validate_user;
        }
        $user=$userFunction->save_user($request,$user,2);
        $msg=$lang == 'en' ?  'Edited successfully' :'تم التعديل بنجاح' ;
        return $this->apiResponseData(new UserResource($user),  $msg);
    }

    /*
     * upoad image for user
     */
    public function save_image(Request $request)
    {
        $user=Auth::user();
        $lang=$request->header('lang');
        if($request->image){
            BaseController::deleteFile('users',$user->image);
            $name=BaseController::saveImage('users',$request->file('image'));
            $user->image=$name;
        }else{
            $msg=$lang=='ar' ? 'من فضلك ارفع الصورة' : 'please upload image';
            return $this->apiResponseMessage(0,$msg,200);
        }
        $user->save();
        $msg=$lang=='ar' ? 'تم رفع الصورة بنجاح' : 'image uploaded successfully';
        return $this->apiResponseData(new UserResource($user),$msg,200);

    }

    /*
     * get user information from token auth
     */
    public function my_info(Request $request)
    {
        $lang = $request->header('lang');
        $user=Auth::user();
        $msg=$lang=='ar' ?  'تمت العملية بنجاح' :'success' ;
        return $this->apiResponseData(new UserResource($user),$msg);
    }


    /*
     *@pram Email to check exist in database
     *@return  if exist send code to email , not exist sent error message
     */

    public function forget_password(Request $request){
        $user=User::where('email',$request->email)->first();
       if(is_null($user)){
           return $this->apiResponseMessage(0,'البريد الالكتروني غير موجود لدينا',200);
       }
        $code=mt_rand(999,9999);
        $user->code=$code;
        $user->save();
        EmailsController::forget_password($user,'ar');
        $token = $user->createToken('TutsForWeb')->accessToken;
        $user['token_user']=$token;
        return $this->apiResponseData(new UserResource($user),'تم ارسال كود اعادة كلمة السر الي بريدك الالكتروني',200);

    }

    /*
     * @pram code , new password
     * @return if code incorrect error message , elseif correct change password successfully
     */
    public function reset_password(Request $request)
    {
        $user=Auth::user();
        if(!$request->password){
            $msg='من فضلك ادخل كلمة السر الجديدة' ;
            return $this->apiResponseMessage(0,$msg,200);
        }
        if($request->password != $request->password_confirm){
            $msg=  'كلمتا السر غير متطابقتان'  ;
            return $this->apiResponseMessage(0,$msg,200);
        }
        $user->password=Hash::make($request->password);
        $user->save();
        $msg= 'تم تغيير كلمة السر بنجاح' ;
        return $this->apiResponseMessage(1,$msg,200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function check_password_code(Request  $request)
    {
        $user=Auth::user();
        if($request->code != $user->code)
        {
            $msg='الكود غير صحيح' ;
            return $this->apiResponseMessage(0,$msg,200);
        }
        $user->code=null;
        $user->save();
        $msg= 'الكود صحيح' ;
        return $this->apiResponseMessage(1,$msg,200);
    }

}
