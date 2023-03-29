<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Route::group(["prefix" => "goods", "namespace" => "Goods", 'middleware' => ['admin.token.check']], function () {
    Route::group(["prefix" => "cat"], function () {
        //添加商品分类
        Route::post("add", "CategoryController@add");
        //编辑商品分类
        Route::post("edit", "CategoryController@edit");
        //商品分类列表
        Route::get("list", "CategoryController@list");
        //商品颜色列表
        Route::get("colors-list", "CategoryController@colorList");
    });

    //商品标签列表
    Route::get("tag/list", "IndexController@tags");

    //添加商品
    Route::post("add", "IndexController@add");

    //商品列表
    Route::post("list", "IndexController@list");

    //商品详情
    Route::post("info", "IndexController@goodsInfo");

    //删除商品
    Route::post("delete", "IndexController@delete");

    //商品售罄
    Route::post("sold-out", "IndexController@soldOut");

    //编辑商品
    Route::post("edit", "IndexController@edit");

    //编辑商品标签
    Route::post("edit/tag", "IndexController@editTag");
});
