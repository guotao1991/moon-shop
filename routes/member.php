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

Route::group(["prefix" => "member", "namespace" => "Member", 'middleware' => ['admin.token.check']], function () {
    //用户列表
    Route::post("list", "IndexController@userList");
    //用户详情
    Route::post("info", "IndexController@userInfo");
    //用户标签列表
    Route::get("tag/list", "IndexController@tagList");
    //创建用户
    Route::post("add", "IndexController@createUser");
    //编辑用户
    Route::post("edit", "IndexController@editUser");
    //编辑用户备注
    Route::post("edit-remark", "IndexController@editRemark");
    //编辑用户标签
    Route::post("edit-tags", "IndexController@editTag");
    //用户分析
    Route::post("system-analysis", "IndexController@systemAnalysis");
});
