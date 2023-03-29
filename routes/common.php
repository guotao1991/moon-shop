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

Route::group(['prefix' => "common", "namespace" => "Common"], function () {
    //获取图片验证码
    Route::get("captcha", "IndexController@verifyCode");
    //发送短信验证码
    Route::post("send/code", "IndexController@sendSmsCode");
    //上传图片
    Route::post("upload/img", "IndexController@uploadImg");
    //获取省市区联动数据
    Route::get("region/list", "DistrictController@getDistrictList");
});
