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

Route::any('/', 'CountController@index');
Route::any('index', 'CountController@index');

Route::any('download', 'CountController@download');

Route::any('count/index', 'CountController@calculation');
