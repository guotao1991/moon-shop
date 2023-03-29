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
Route::group(["prefix" => '/v1'], function () {
    //用户模块
    Route::group(["prefix" => '/user'], function () {
        //注册用户
        Route::post("register", "V1\Store\UserController@appRegister");
        //忘记密码
        Route::post("mobile/login", "V1\Common\UserController@login");
        //发送验证码
        Route::post("send/code", "V1\Common\CommonController@sendSmsCode");
        //获取验证码
        Route::get("verify/code", "V1\Common\CommonController@verifyCode");
        //忘记密码
        Route::post("forget/pass", "V1\Common\UserController@forgetPass");
    });

    Route::group(["prefix" => '/user', "middleware" => ["auth"]], function () {
        //修改密码
        Route::post("update/pass", 'V1\Common\UserController@updatePass');
        //获取用户拥有的菜单
        Route::get("/menu", 'V1\Store\UserController@getUserMenu');
    });

    Route::group(["prefix" => '/store', "middleware" => ["auth"]], function () {
        //创建店铺
        Route::post("create", "V1\Store\IndexController@create");
        //店铺列表
        Route::get("list", 'V1\Store\IndexController@getStoreList');
        //店铺详情
        Route::get("info/{storeId}", 'V1\Store\IndexController@getStoreInfo');
        //店铺详情
        Route::get("sale/statistics/{storeId}", 'V1\Store\IndexController@getStoreSalesStatistics');
        //获取用户主店信息
        Route::get("main", 'V1\Store\IndexController@getMainStore');
        //banner图列表
        Route::get("banner/list", "V1\Store\BannerController@getBanners");
        //修改店铺信息
        Route::post("update", "V1\Store\IndexController@update");
        //获取店铺主页折线图数据
        Route::get("chart/data/{storeId}", "V1\Store\IndexController@getStoreChartData");
        //注册
        Route::post("register", "V1\Store\UserController@appRegister");
    });

    Route::group(['prefix' => 'member', "middleware" => ["auth"]], function () {
        //商家的会员列表
        Route::get("list", "V1\Member\IndexController@list");
    });

    Route::group(['prefix' => '/common'], function () {
        //获取省市区列表
        Route::get("district/list", 'V1\Common\CommonController@getDistrictList');
        //上传图片
        Route::post("upload/img", 'V1\Common\CommonController@uploadImg');
    });
});
