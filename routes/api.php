<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Http\Request;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');

header('Content-Type: application/json; charset=UTF-8', true);


/** Start Auth Route **/

Route::middleware('auth:api')->group(function () {
    //Auth_private
    Route::prefix('Auth_private')->group(function () {
        Route::post('/change_password', 'Api\UserController@change_password')->name('user.change_password');
        Route::post('/edit_profile', 'Api\UserController@edit_profile')->name('user.edit_profile');
        Route::post('/check_password_code', 'Api\UserController@check_password_code')->name('user.check_password_code');
        Route::get('/my_info', 'Api\UserController@my_info')->name('user.my_info');
        Route::post('/reset_password', 'Api\UserController@reset_password')->name('user.reset_password');
        Route::post('/check_active_code', 'Api\UserController@check_activation_code')->name('user.check_activation_code');
    });


//general Auth
    Route::prefix('Resource')->group(function () {
        Route::post('/add_resource', 'Api\ResourceController@add_resource')->name('Resource.get_resources');
        Route::get('/get_my_resource', 'Api\ResourceController@get_my_resource')->name('Resource.get_my_resource');
    });
});
/** End Auth Route **/

//general Auth
Route::prefix('Auth_general')->group(function () {
    Route::post('/register', 'Api\UserController@register')->name('user.register');
    Route::post('/login', 'Api\UserController@login')->name('user.login');
    Route::get('/check_code/{id}', 'Api\UserController@check_virfuy')->name('user.check_virfuy');
    Route::post('/forget_password', 'Api\UserController@forget_password')->name('user.forget_password');
});


//general Auth
Route::prefix('News')->group(function () {
    Route::get('/home', 'Api\NewsController@home')->name('News.home');
    Route::get('/news_by_city', 'Api\NewsController@news_by_city')->name('News.news_by_city');
    Route::get('/news_by_source', 'Api\NewsController@news_by_source')->name('News.news_by_source');
    Route::get('/news_by_cat', 'Api\NewsController@news_by_cat')->name('News.news_by_cat');
    Route::get('/source_by_city', 'Api\NewsController@source_by_city')->name('News.source_by_city');
    Route::get('/single_new/{new_id}', 'Api\NewsController@single_new')->name('News.single_new');
    Route::get('/filter_news', 'Api\NewsController@filter_news')->name('News.filter_news');
});

//general Auth
Route::prefix('Resource')->group(function () {
    Route::get('/get_resources', 'Api\ResourceController@get_resources')->name('Resource.get_resources');
    Route::get('/masreqNews', 'Api\ResourceController@masreqNews')->name('Resource.masreqNews');
});
