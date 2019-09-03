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


Route::get('/', 'HomeController@home');
Route::get('index', 'HomeController@index');
Route::post('timeZone', 'HomeController@timeZone');//首页切换时区
Route::get('getTime','HomeController@getTime');
//上传文件
Route::any('upload/file', 'UploadController@file');

//统计中心
Route::group(['prefix' => 'statistical_center'], function (){                //统计中心
    Route::any('index','StatisticalController@index');                       //入库单统计
    Route::any('list','StatisticalController@statiscalList');                       //入库单统计

});

//任务中心
Route::group(['prefix' => 'task_center'], function (){                            //任务中心
    Route::group(['prefix' => 'download_task'] ,function (){                      //下载任务
        Route::any('index','DownloadTaskController@index');                       //下载任务首页
        Route::any('download_list','DownloadTaskController@downloadList');        //下载任务列表
        Route::any('download_del','DownloadTaskController@downloadDel');          //删除下载任务
        Route::any('download_export/{id}','DownloadTaskController@downloadExport');          //下载数据导出
//        Route::any('down','DownloadTaskController@down');
    });
    Route::group(['prefix' => 'task_config'] ,function (){       //任务配置
        Route::any('index','TaskConfigController@index');        //任务配置首页
        Route::any('store','TaskConfigController@store');        //任务配置-通知保存
        Route::any('cleanup_cycle_store','TaskConfigController@cleanupCycleStore');        //任务配置-删除周期保存
    });
});

//预约单管理
Route::group(['prefix'=>'reservation_management'],function (){
    Route::any('index','ReservationManagementController@index');//预约单管理页面
    Route::any('search','ReservationManagementController@search');//预约单管理页面搜索
    Route::any('searchInbound','ReservationManagementController@searchInbound');
    Route::any('getEditInboundOrder','ReservationManagementController@getEditInboundOrder');//获取预约单编辑页面的入库单信息
    Route::any('create','ReservationManagementController@create');//预约单创建页面
    Route::any('addOrUpdate','ReservationManagementController@addOrUpdate');//预约单创建或者更新
    Route::any('edit','ReservationManagementController@edit');//预约单编辑页面
    Route::any('discard','ReservationManagementController@discard');//废弃预约单
    Route::any('appointmentReview','ReservationManagementController@appointmentReview');//预约审核预约单
    Route::any('review','ReservationManagementController@review');//审核预约单页面
    Route::any('updateReview','ReservationManagementController@updateReview');//审核预约单更改状态
    Route::any('detail','ReservationManagementController@detail');//查看预约单
    Route::any('review','ReservationManagementController@review');//审核预约单
    Route::any('upload','ReservationManagementController@upload');//上传文件
    Route::any('export','ReservationManagementController@export');//导出文件
});

//接口
Route::group(['prefix'=>'api'], function () {
    Route::any('reservationCreate', 'Api\ReservationApiController@reservationCreate');//谷仓创建预约单接收接口
    Route::any('reservationUpdate', 'Api\ReservationApiController@reservationUpdate');//谷仓创建预约单接收接口
    Route::any('updateReservationStatus', 'Api\ReservationApiController@updateReservationStatus');//谷仓更新预约单状态接收接口
    Route::any('searchReservationOrder', 'Api\ReservationApiController@searchReservationOrder');//谷仓查询预约单接口
    Route::any('reservationNumberDetail', 'Api\ReservationApiController@reservationNumberDetail');//谷仓查看预约单详情接口
});

//还柜单
Route::group(['prefix'=>'return_cabinet'],function (){
    Route::any('index','ReturnCabinetController@index');//还柜单管理页面
    Route::any('look','ReturnCabinetController@look');//还柜单查看页面
    Route::any('emiltext','ReturnCabinetController@emiltext');//还柜单邮件发送页面
    Route::any('emil','ReturnCabinetController@emil');//还柜单邮件处理页面
    Route::any('search','ReturnCabinetController@search');//还柜单搜索
    Route::any('export','ReturnCabinetController@export');//还柜单导出
    Route::any('down','ReturnCabinetController@down');//还柜单导出
});

//菜单管理
Route::group(['prefix'=>'menu_management'], function () {
    Route::any('index','MenuManagementController@index'); //菜单管理首页
    Route::any('store_menu','MenuManagementController@storeMenu'); //菜单管理保存
    Route::any('edit/{id}','MenuManagementController@edit'); //菜单管理编辑页面
    Route::any('del_menu','MenuManagementController@delMenu'); //菜单管理编删除菜单
});

//pda
Route::group(['prefix' => 'pda'], function() {
    Route::any('index', 'PdaController@index');//pda首页
    Route::any('appointment', 'PdaController@appointment');//pda预约完结显示页面
    Route::any('ajaxInputAppointmentNum/{num}', 'PdaController@ajaxInputAppointmentNum');//pda预约、卸柜、还柜完结输入单号
    Route::any('appointment_submit', 'PdaController@appointmentSubmit');//pda预约完结提交
    Route::any('unloading', 'PdaController@unloading');//pda卸货完结显示页面
    Route::any('unloading_submit', 'PdaController@unloadingSubmit');//pda卸货完结提交
    Route::any('cabinet', 'PdaController@cabinet');//pda还柜完结显示页面
    Route::any('cabinet_submit', 'PdaController@cabinetSumbit');//pda还柜完结提交
});
//用户中心
Route::group(['prefix'=>'user_center'], function () {
    Route::any('index', 'UserCenterController@index');//用户中心页面
    Route::any('search', 'UserCenterController@search');//用户中心搜索
    Route::any('userlook', 'UserCenterController@userlook');//用户中心查看
    Route::any('edit', 'UserCenterController@edit');//用户中心查看
    Route::any('warehouse', 'UserCenterController@warehouse');//用户中心编辑
    Route::any('auth', 'UserCenterController@auth');//用户中心权限查看
    Route::any('assignAccess', 'UserCenterController@assignAccess');//用户中心权限分配
});

//预约送仓
Route::group(['prefix'=>'reservation_code'], function () {
    Route::any('index', 'ReservationCodeController@index')->middleware('reservationCode');//首页
    Route::any('UserIndex', 'ReservationCodeController@UserIndex');//登录首页
    Route::any('create', 'ReservationCodeController@create');//预约码验证
    Route::any('add', 'ReservationCodeController@add');//详情页
    Route::any('UserAdd', 'ReservationCodeController@UserAdd');//详情页
    Route::any('update', 'ReservationCodeController@update');//数据更改
});

//角色管理
Route::group(['prefix'=>'role'], function () {
    Route::any('index', 'RoleController@index');//角色管理页面
    Route::any('search','RoleController@search');//角色搜索
    Route::any('addAndUpdate','RoleController@addAndUpdate');//新增修改角色
    Route::any('stop','RoleController@stop');//停用角色
    Route::any('start','RoleController@start');//启用角色
    Route::any('delete','RoleController@delete');//删除角色
    Route::any('giveAccess','RoleController@giveAccess');//查看权限
    Route::any('assignAccess','RoleController@assignAccess');//分配权限
});

Route::get('testRedis','RedisController@testRedis')->name('testRedis');
Route::get('bubble','RedisController@bubble')->name('bubble');

