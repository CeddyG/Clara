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
