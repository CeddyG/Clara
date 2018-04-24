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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'access:api'], function()
{
    //Api routes for datatables and select2
    $aConfig = config('clara.route.api');
    
    foreach ($aConfig as $sRoute => $sName)
    {
        Route::get($sRoute.'/index/ajax', $sName.'Controller@indexAjax')->name('admin.'.$sRoute.'.index.ajax');
        Route::get($sRoute.'/select/ajax', $sName.'Controller@selectAjax')->name('admin.'.$sRoute.'.select.ajax');
    }
});