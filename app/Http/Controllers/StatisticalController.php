<?php

namespace App\Http\Controllers;

use App\Auth\Common\CurrentUser;
use App\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Services\ReservationManagementService;
class StatisticalController extends BaseAuthController
{

    /**
     * 入库单统计首页
     * @author zt7242
     * @date 2019/5/9 18:08
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime = $currentUser->wareTime;
        if($wareTime == '-12'){
            $wareCode = 'USEA';
        }else{
            $wareCode = 'USWE';
        }

//        $warehouse = ReservationManagementService::getWarehouse();//获取仓库
        $reservationStatus = ReservationManagementService::getReservationStatus();//获取预约状态
        return view('statisticalCenter.index',compact('wareCode','reservationStatus'));
    }


    public function statiscalList(Request $request)
    {
        $info = $request->all();
        $data = isset($info['data']) ? $info['data'] : '';
//        dd($info);
        if ($request->has('page')) {
            $current_page = $request->input('page');
            $current_page = $current_page <= 0 ? 1 :$current_page;
        } else {
            $current_page = 1;
        }
        $limit = $info['limit']??'10';

        $statistiacl = ReservationManagementService::getStatistiaclInfo($data, $limit, $current_page);
        $res = array(
            'code' => '0',
            'msg' =>'',
            'count' => $statistiacl['count'],
            'data' => $statistiacl['item'],
            'reservation'=>$statistiacl['reservation'],
            'box'=>$statistiacl['box'],
            'sku'=>$statistiacl['sku'],
            'product'=>$statistiacl['product'],
            'dataBar'=>$statistiacl['dataBar'],
            'timeBar'=>$statistiacl['timeBar'],
        );
        return Response()->json($res);
    }
}
