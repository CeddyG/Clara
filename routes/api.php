<?php

use Illuminate\Http\Request;

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

Route::get('dataflow/{format}/{token}/{param?}', 'DataflowController@index');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'api'], function()
{
    //Api routes for datatables
    //DummyIndex
    
    Route::get('dataflow/index/ajax', 'DataflowController@indexAjax')->name('admin.dataflow.index.ajax');
	Route::get('dataflow/select/ajax', 'DataflowController@selectAjax')->name('admin.dataflow.select.ajax');
});