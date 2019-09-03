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
// 登录
Route::get('/login', 'LoginController@index')->name("login");
Route::post('/auth/doLogin', 'LoginController@doLogin');
Route::post('/auth/bisLogin', 'LoginController@bisLogin');
Route::get('/auth/verifyCode', 'LoginController@verifyCode');
Route::post('/auth/login/requiredVerifyCode', 'LoginController@requiredVerifyCode');
Route::get('/logout', 'LoginController@logout')->name("logout");
Route::get('/pda/login', 'LoginController@pdaLogin');
Route::post('/auth/warehouse', 'LoginController@warehouse');