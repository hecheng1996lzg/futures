<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * 欢迎页
 **/
Route::any('/', 'IndexController@index'); //显示主页
Route::any('/index', 'IndexController@index'); //显示主页


/**
 * 品种
 * △显示新增页面
 * △新增操作
 * △显示更新页面
 * △更新一个数据
 * △更新所有
 * △显示一个品种
 **/
Route::group([],function (){
    Route::get('variety/add','VarietyController@create');               //显示新增页面
    Route::post('variety/add','VarietyController@store');               //新增操作
    Route::get('variety/update','VarietyController@edit');              //显示更新页面
    Route::post('variety/update','VarietyController@update');           //更新一个数据
    Route::get('variety/update/all','VarietyController@update_all');    //显示更新所有页面
    Route::post('variety/update/all','VarietyController@update_all_save');   //更新所有
    Route::any('variety/{id}','VarietyController@show');                //显示一个数据
});

/**
 * 下载
 **/
Route::any('/download', 'IndexController@download'); //下载

/**
 * 保存选择区域
 **/
Route::any('/selection', 'IndexController@selection'); //保存选择区域
