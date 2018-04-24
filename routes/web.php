<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//Sentinel login
Route::get('login', 'Admin\UserController@login');
Route::post('authenticate', 'Admin\UserController@authenticate');
Route::get('logout', 'Admin\UserController@logout');

Route::get('/', function () {
    return view('welcome');
});

//Admin
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'access'], function()
{
    Route::get('/', 'HomeController@index')->name('admin');
    
    //App Controllers
    $aConfig = config('clara.route.admin');
    
    foreach ($aConfig as $sRoute => $sName)
    {
        Route::resource($sRoute, $sName.'Controller', ['names' => 'admin.'.$sRoute]);
    }

    Route::resource('user', 'UserController', ['names' => 'admin.user']);
    Route::resource('group', 'RoleController', ['as' => 'admin']);
});
