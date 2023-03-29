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
        "prefix" => "hq",
        "namespace" => "Hq",
        'middleware' => ['user.token.check', 'admin.check']
    ],
    function () {
        //HQ列表
        Route::get("list", "HqController@hqList");
    }
);
