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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/weixin/userinfo/{openid}','Weixin\WxController@getUserInfo');
Route::get('/weixin/gettag','Weixin\WxController@getTag');
Route::get('/weixin/test','Weixin\WxController@test');


Route::get('/weixin/valid1','Text\TextController@validToken1');
Route::post('/weixin/valid1','Text\TextController@wxEvent');
Route::get('/weixin/gettoken','Text\TextController@getAccessToken');
Route::get('/weixin/getuserinfo','Text\TextController@getUserInfo');
Route::get('/weixin/userlist','Text\TextController@userList');

Route::get('/weixin/gettag','Text\TextController@gettag');
Route::get('/weixin/lahei/{id}','Text\TextController@lahei');
Route::get('/weixin/taglist','Text\TextController@taglist');
Route::get('/weixin/getusertag','Text\TextController@getusertag');