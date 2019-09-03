<?php

namespace App\Http\Controllers;

use App\Auth\Common\AjaxResponse;
use App\Auth\Controllers\BaseAuthController;
use App\Models\Warehouse;
use App\Services\RouteService;
use Illuminate\Http\Request;
use App\Services\ReservationManagementService;
use App\Auth\Common\CurrentUser;
use App\Services\LanguageService;

class HomeController extends BaseAuthController
{

    public function home()
    {
        $currentUser = CurrentUser::getCurrentUser();
        $user_code = $currentUser->userCode;
        $user_id = $currentUser->userId;
        $ware_code = $currentUser->wareTime;
        $wareHouse = $this->getWarehouseNameByTimeZone($ware_code);

        //获取菜单结构
        $list = RouteService::getNavigationNodeByUserId($user_id);
        $language = LanguageService::getAll();

        if (session()->has('oldTimeZone') == false) {
            session(['oldTimeZone' => array('time_zone' => '8', 'area' => __('auth.china'))]);
        }

        return view('home')->with([
            'list' => $list,
            'user_code' => $user_code,
            'ware_house_time_zone' => $ware_code,
            'ware_house' => $wareHouse,
            'old_time_zone' => session('oldTimeZone')['time_zone'],
            'old_ware_house' => session('oldTimeZone')['area']
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statistiacl = ReservationManagementService::indexStatistiacl();
        return view('index.index',compact('statistiacl'));
    }

    /*
     * 根据时区获得仓库名称
     * zt3361
     */
    private function getWarehouseNameByTimeZone($timeZone){
        $wareHouse = '';
        if($timeZone== '-12'){
            $wareHouse =  __('auth.USEastWarehouse');
        }elseif($timeZone== '-15'){
            $wareHouse = __('auth.USWestWarehouse');
        }else{
            $wareHouse = __('auth.china');
        }
        return $wareHouse;
    }

    /**
     * 切换时区
     * @author zt12700
     */
    public function timeZone(Request $request)
    {
        $timeZone = $request->input('timeZone');
        $currentUser = CurrentUser::getCurrentUser();

        session(['oldTimeZone'=>array('time_zone' => $currentUser->wareTime,
            'area' => $this->getWarehouseNameByTimeZone($currentUser->wareTime))]);
        $currentUser->wareTime = $timeZone;
        CurrentUser::setCurrentUser($currentUser);
        return AjaxResponse::isSuccess();
    }

    /**
     * 获取服务器时间
     * @author zt3361
     */
    public function getTime()
    {
        $utc_8 = date("Y-m-d H:i", time());

        $date = date_create(Warehouse::switchTimeByZone($utc_8));
        $warehouse = date_format($date, "Y-m-d H:i");
        $time = array("utc_8" => $utc_8, "warehouse" => $warehouse);
        return json_encode($time);
    }
}
