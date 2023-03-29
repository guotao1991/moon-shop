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

Route::group(["prefix" => "user", "namespace" => "User", 'middleware' => ['user.token.check']], function () {
    //用户列表
    Route::post("edit", "IndexController@edit");
    //获取角色详情
    Route::get("info", "IndexController@userInfo");
    //授权手机号
    Route::post("authorization/mobile", "IndexController@authorizationMobile");
});
