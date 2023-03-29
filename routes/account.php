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

//公共接口
Route::group(["prefix" => '/v1', 'namespace' => 'V1'], function () {
    //账户相关
    Route::group(["prefix" => "account", "namespace" => "Admin"], function () {
        //微信登录
        Route::post("login-by-wx", "AccountController@loginByWx");
        //开发账号登录
        Route::post("login-dev", "AccountController@loginDev");
    });

    Route::group(["prefix" => "account", "namespace" => "Admin", 'middleware' => ['admin.token.check']], function () {
        //修改密码
        Route::post("update-pass", "AccountController@updatePass");
        //获取账号类型
        Route::get("admin-type/{storeId}", "AccountController@adminType");
        //添加管理员
        Route::post("add-manager", "AccountController@addManager")->middleware(['hq.admin']);
    });
});
