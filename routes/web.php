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

//微信公众号
Route::group(['middleware'=>['web']],function() {
    Route::any('checkSignature', ['uses' => 'weixinController@checkSignature']);
    Route::any('responseMsg', ['uses' => 'weixinController@responseMsg']);
    Route::any('http_curl',['uses'=>'weixinController@http_curl']);
    Route::any('getWxAccessToken',['uses'=>'weixinController@getWxAccessToken']);
    Route::any('getWxServerIp',['uses'=>'weixinController@getWxServerIp']);
    Route::any('definedItem',['uses'=>'weixinController@definedItem']);
    Route::any('getWeatherToken',['uses'=>'weixinController@getWeatherToken']);
    Route::any('sendMsgAll',['uses'=>'weixinController@sendMsgAll']);
    Route::any('getBaseInfo',['uses'=>'weixinController@getBaseInfo']);
    Route::any('getUserOpenId',['uses'=>'weixinController@getUserOpenId']);
    Route::any('getUserDetail',['uses'=>'weixinController@getUserDetail']);
    Route::any('getUserInfo',['uses'=>'weixinController@getUserInfo']);
    Route::any('sendTemplateMsg',['uses'=>'weixinController@sendTemplateMsg']);
    Route::any('getTimeOrCode',['uses'=>'weixinController@getTimeOrCode']);
    Route::any('getForeverOrCode',['uses'=>'weixinController@getForeverOrCode']);
    Route::any('shareWx',['uses'=>'weixinController@shareWx']);
    Route::any('getJsApiTicket',['uses'=>'weixinController@getJsApiTicket']);
    Route::any('test',['uses'=>'weixinController@test']);


});
