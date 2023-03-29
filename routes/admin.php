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

Route::group(["prefix" => "admin", "namespace" => "Admin"], function () {
    //账户相关
    Route::group(["prefix" => "account"], function () {
        //商家登录
        Route::post("login-by-code", "AccountController@loginByCode");
        //商家密码登录
        Route::post("login-by-pass", "AccountController@loginByPass");
        //忘记密码
        Route::post("forget-pass", "AccountController@forgetPass");
        //忘记密码
        Route::post("login-by-wx", "AccountController@loginByWx");
        //开发账号登录
        Route::post("login-dev", "AccountController@loginDev");
    });


    Route::group(["prefix" => "account", 'middleware' => ['admin.token.check']], function () {
        //修改密码
        Route::post("update-pass", "AccountController@updatePass");
        //获取账号类型
        Route::get("admin-type/{storeId}", "AccountController@adminType");
        //添加管理员
        Route::post("add-manager", "AccountController@addManager")->middleware(['hq.admin']);
    });

    //权限相关
    Route::group(["prefix" => "role", 'middleware' => ['admin.token.check']], function () {
        //获取角色列表
        Route::get("list", "RoleController@roleList")->middleware(['hq.admin']);
        //获取角色详情
        Route::get("info/{roleId}", "RoleController@roleInfo");
    });
});
