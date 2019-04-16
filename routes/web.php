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

Route::get('/', function () {
    return view('welcome');
});


//echostr
Route::get('valid', 'wxController\WxController@valid'); 
Route::post('valid', 'wxController\WxController@valids'); 

//推送
Route::post('valid', 'wxController\WxController@wxEvent');

Route::get('accesstoken', 'wxController\WxController@accesstoken');
//Route::any('test','wxController\WxController@test');