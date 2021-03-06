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

//推送
Route::post('valid', 'wxController\WxController@wxEvent');

Route::get('accesstoken', 'wxController\WxController@accesstoken');
//Route::any('test','wxController\WxController@test');
//微信支付测试
Route::get('test', 'wxController\WxPayController@test');
//notify
Route::get('notify', 'wxController\WxPayController@notify');