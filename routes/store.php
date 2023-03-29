<?php

use Illuminate\Support\Facades\Route;

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

Route::group(
    [
        "prefix" => "store",
        "namespace" => "Store",
        'middleware' => ['user.token.check','admin.check']
    ],
    function () {
        Route::get("list", "IndexController@storeList");
        Route::get("info/{storeId}", "IndexController@storeInfo");
        Route::get("default", "IndexController@defaultStore");
        //创建店铺
        Route::post("add", "IndexController@create");
        Route::post("update", "IndexController@update");
        //进入HQ
        Route::get("into-hq/{hqId}", "IndexController@intoHq");
        //进入店铺
        Route::get("into-store/{storeId}", "IndexController@intoStore");
    }
);
