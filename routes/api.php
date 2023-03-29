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
    /**
     * 账号模块
     * 包括用户登录相关
     */
    require base_path('routes/account.php');

    /**
     * 管理员模块
     */
    require base_path('routes/admin.php');

    /**
     * 店铺模块
     */
    require base_path('routes/store.php');

    /**
     * HQ模块
     */
    require base_path('routes/hq.php');

    /**
     * 会员模块
     */
    require base_path('routes/member.php');

    /**
     * 客户模块
     */
    require base_path('routes/user.php');

    /**
     * 公共模块
     */
    require base_path('routes/common.php');

    /**
     * 商品模块
     */
    require base_path('routes/goods.php');

    /**
     * 微信接口
     */
    require base_path('routes/wechat.php');

    /**
     * 系统模块
     */
    require base_path('routes/system.php');
});
