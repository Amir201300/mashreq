<?php

Route::post('/admin/login','Manage\MainController@login')->name('admin.login');


Route::group(['prefix' => LaravelLocalization::setLocale(),

'middleware' => ['localeSessionRedirect','localizationRedirect','localeViewPath']] ,  function()

{
    Route::prefix('manage')->group(function()
    {
        Route::get('/login' , function(){
            return view('manage.loginAdmin');
          });
          Route::group(['middleware' => 'roles' , 'roles' => ['SuperAdmin','Admin','rant','daleel','house']], function ()
          {



            Route::get('/logout/logout','Manage\MainController@logout')->name('user.logout');
            Route::get('/home', 'Manage\MainController@index')->name('admin.dashboard');

            // Profile Route
            Route::prefix('profile')->group(function()
            {
                Route::get('/index', 'Manage\profileController@index')->name('profile.index');
                Route::post('/index', 'Manage\profileController@update')->name('profile.update');
            });

              //Category routes
              Route::prefix('Category')->group(function () {
                  Route::get('/index', 'Manage\CategoryController@index')->name('Category.index');
                  Route::get('/view', 'Manage\CategoryController@view')->name('Category.view');
                  Route::post('/store', 'Manage\CategoryController@store')->name('Category.store');
                  Route::get('/show/{id}', 'Manage\CategoryController@show')->name('Category.show');
                  Route::post('/update', 'Manage\CategoryController@update')->name('Category.update');
                  Route::get('/delete/{id}', 'Manage\CategoryController@delete')->name('Category.delete');
              });

              //Cities routes
              Route::prefix('Cities')->group(function () {
                  Route::get('/index', 'Manage\CitiesController@index')->name('Cities.index');
                  Route::get('/view', 'Manage\CitiesController@view')->name('Cities.view');
                  Route::post('/store', 'Manage\CitiesController@store')->name('Cities.store');
                  Route::get('/show/{id}', 'Manage\CitiesController@show')->name('Cities.show');
                  Route::post('/update', 'Manage\CitiesController@update')->name('Cities.update');
                  Route::get('/delete/{id}', 'Manage\CitiesController@delete')->name('Cities.delete');
              });

              //Sources routes
              Route::prefix('Sources')->group(function () {
                  Route::get('/index', 'Manage\SourcesController@index')->name('Sources.index');
                  Route::get('/view', 'Manage\SourcesController@view')->name('Sources.view');
                  Route::post('/store', 'Manage\SourcesController@store')->name('Sources.store');
                  Route::get('/show/{id}', 'Manage\SourcesController@show')->name('Sources.show');
                  Route::post('/update', 'Manage\SourcesController@update')->name('Sources.update');
                  Route::get('/delete/{id}', 'Manage\SourcesController@delete')->name('Sources.delete');
              });
        });
    });
});

