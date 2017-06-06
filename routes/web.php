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

Route::get('install', 'InstallController@installBdd');
Route::post('install', 'InstallController@storeBdd');

//Sentinel login
Route::get('login', 'Admin\UserController@login');
Route::post('authenticate', 'Admin\UserController@authenticate');
Route::get('logout', 'Admin\UserController@logout');

Route::get('/', function () {
    return view('welcome');
});

//Admin
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'log'], function()
{
    Route::get('/', 'HomeController@index');

    Route::group(['middleware' => 'access'], function()
    {
        //Controllers de l'appli
        //DummyControllers

        Route::resource('user', 'UserController', ['names' => 'admin.user']);
        Route::resource('group', 'RoleController', ['as' => 'admin']);
        Route::resource('entity', 'EntityController',
        [
            'only' => ['index', 'store'],
            'as' => 'admin'
        ]
        );
    });
});
