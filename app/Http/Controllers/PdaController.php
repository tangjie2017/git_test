<?php

namespace App\Http\Controllers;

use App\Auth\Common\AjaxResponse;
use App\Auth\Common\CurrentUser;
use App\Models\ReturnCabinet;
use App\Services\ReturnCabinetService;
use Illuminate\Http\Request;
use App\Auth\Controllers\BaseAuthController;
use App\Models\StaticState;
use App\Services\ReservationManagementService;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Services\LanguageService;

class PdaController extends BaseAuthController
{
    /**
     * PDA首页
     * @author zt6768
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('pda.index');
    }

    /**
     * /pda预约完结显示页面
     * @author zt7242
     * @date 2019/5/7 9:38
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function appointment()
    {
        return view('pda.appointment');
    }

    /**
     * 验证预约单号或海柜号
     * @author zt7242
     * @date 2019/5/8 13:34
     * @param Request $request
     * @param $num
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxInputAppointmentNum(Request $request,$num)
    {
        if (empty($num)) {
            return AjaxResponse::isFailure(__('auth.requestParameterEmpty'));
        }

        $exitsWarehouse = ReservationManagementService::warehouseExist($num);

        if(!$exitsWarehouse){
            return AjaxResponse::isFailure(__('auth.PleaseCorrespondingWarehouse'));
        }

        //通过单号查询预约单号或海柜号(跟踪号)是否存在
        $exist = ReservationManagementService::ReservationOrArknumberExist($num);
        if($exist->isEmpty()){
            return AjaxResponse::isFailure(__('auth.noNumberExistsSystem'));
        }

        $ids = [];
        //跟踪号可能对应多个预约单
        foreach($exist as $item){
            $ids[] = $item->reservation_number_id;
        }
//        $reservation_number_id = $exist->reservation_number_id;

        if(empty($ids)){
            return AjaxResponse::isFailure(__('auth.noNumberExistsSystem'));
        }
        $type = $request->input('type');

        if($type == 1){
            //验证预约单号和海柜号在预约单管理是否为待送仓状态，预约状态为生效中(预约完结页面)
            $realIds = [];
            foreach($ids as $id){
                $reservationInfo = ReservationManagementService::getReservationInfoById($id);
                if(empty($reservationInfo)){
                    return AjaxResponse::isFailure(__('auth.dataError'));
                }
                if($reservationInfo->status !== StaticState::STATUS_WAIT_SEND_WAREHOUSE || $reservationInfo->reservation_status !== StaticState::RESERVATION_STATUS_EFFECTIVE){
                    continue;
                }
                $realIds[] = $id;
            }

            $count = count($realIds);
            if($count == 0){
                return AjaxResponse::isFailure(__('auth.numberStateError'));
            }
            if($count > 0 && count($ids) !== $count){
                return AjaxResponse::isFailure(__('auth.numberStateError'));
//                return AjaxResponse::isFailure(__('auth.onlyReservationNumber'));
            }


            return AjaxResponse::isSuccess('',$realIds);

        }elseif ($type == 2){
            $realIds = [];
            foreach($ids as $id){
                $cabinetInfo = ReturnCabinetService::getCabinetInfoByReservationId($id);
                if(empty($cabinetInfo)){
                    return AjaxResponse::isFailure(__('auth.numberStateError'));
                }
                //验证预约单号和海柜号在还柜管理是否为待卸柜状态(卸柜完结页面)
                if(!isset($cabinetInfo->status) || $cabinetInfo->status !== StaticState::RETURN_STATUS_UNLOADING){
                    continue;
                }
                $realIds[] = $cabinetInfo->return_cabinet_id;
            }

            $count = count($realIds);

            if($count == 0){
                return AjaxResponse::isFailure(__('auth.numberStateError'));
            }
            if($count > 0 && count($ids) !== $count){
                return AjaxResponse::isFailure(__('auth.numberStateError'));
//                return AjaxResponse::isFailure(__('auth.onlyReservationNumber'));
            }

            return AjaxResponse::isSuccess('',$realIds);

        }else{
            $realIds = [];
            foreach($ids as $id){
                $cabinetInfo = ReturnCabinetService::getCabinetInfoByReservationId($id);
                if(empty($cabinetInfo)){
                    return AjaxResponse::isFailure(__('auth.numberStateError'));
                }
                //验证预约单号和海柜号在还柜管理是否为待卸柜状态(卸柜完结页面)
                if(!isset($cabinetInfo->status) || $cabinetInfo->status !== StaticState::RETURN_STATUS_RETURN_UNLOADING){
                    continue;
                }
                $realIds[] = $cabinetInfo->return_cabinet_id;
            }

            $count = count($realIds);

            if($count == 0){
                return AjaxResponse::isFailure(__('auth.numberStateError'));
            }
            if($count > 0 && count($ids) !== $count){
                return AjaxResponse::isFailure(__('auth.numberStateError'));
//                return AjaxResponse::isFailure(__('auth.onlyReservationNumber'));
            }

            return AjaxResponse::isSuccess('',$realIds);

        }


    }

    /**
     * 预约完结提交
     * @author zt7242
     * @date 2019/5/8 13:53
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appointmentSubmit(Request $request)
    {
        $files = $request->input('filesInfo')??'';
        $data = $request->input('info');
        $reservation_number_id = $data['reservationId'];
        $actual_arrival_time = $data['actual_arrival_time'];

        $exitsWarehouse = ReservationManagementService::warehouseExistByReservationId($reservation_number_id);

        if(!$exitsWarehouse){
            return AjaxResponse::isFailure(__('auth.PleaseCorrespondingWarehouse'));
        }

        if (empty($files)) {
            return AjaxResponse::isFailure(__('auth.PleaseUploadImage'));
        }
        if (empty($reservation_number_id)) {
            return AjaxResponse::isFailure(__("auth.NoReservationNumber"));
        }
        if (empty($actual_arrival_time)) {
            return AjaxResponse::isFailure(__("auth.ActualArrivalTimeRequired"));
        }

        //更新预约单信息、生成还柜单、插入还柜附件
        $reservation_ids = explode(',',$reservation_number_id);

        //开启事务
        DB::beginTransaction();
        $succ = 0;
        foreach($reservation_ids as $reservation_id){
            $res = ReservationManagementService::updateAndInsertReservationInfo($files,$reservation_id,$actual_arrival_time);
            if($res){
                $succ++;
            }

        }

        if(count($reservation_ids) == $succ){
            DB::commit();
            return AjaxResponse::isSuccess(__('auth.operationSuccess'));
        }else{
            DB::rollBack();
            return AjaxResponse::isFailure(__('auth.operationFailed'));
        }

    }


    /**
     * //pda卸货完结显示页面
     * @author zt7242
     * @date 2019/5/7 9:38
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function unloading()
    {
        return view('pda.unloading');
    }

    /**
     * 卸载完结提交
     * @author zt7242
     * @date 2019/5/8 13:53
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unloadingSubmit(Request $request)
    {
        $data = $request->input('info');
        $cabinet_id = $data['cabinetId'];
        $actual_start_time = $data['actual_start_time'];
        $actual_end_time = $data['actual_end_time'];

        $exitsWarehouse = ReturnCabinetService::warehouseExistByCabinetId($cabinet_id);

        if(!$exitsWarehouse){
            return AjaxResponse::isFailure(__('auth.PleaseCorrespondingWarehouse'));
        }

        if (empty($cabinet_id)) {
            return AjaxResponse::isFailure(__("auth.DidNotFindTheCounter"));
        }

        if (empty($actual_start_time)) {
            return AjaxResponse::isFailure(__('auth.ActualUnloadingStartTimeIsRequired'));
        }

        if (empty($actual_end_time)) {
            return AjaxResponse::isFailure(__('auth.ActualUnloadingEndTimeIsRequired'));
        }

        if($actual_end_time < $actual_start_time){
            return AjaxResponse::isFailure(__('auth.cannotBeLessThanTheActualUnloadingStartTime'));
        }
        //更新还柜单信息并记录还柜日志
        $cabinet_ids = explode(',',$cabinet_id);
        DB::beginTransaction();

        $succ = 0;
        foreach($cabinet_ids as $cabinet_id){
            $res = ReturnCabinetService::updateReturnCabinetInfo($cabinet_id,$actual_start_time,$actual_end_time);
            if($res){
                $succ++;
            }

        }

        if(count($cabinet_ids) == $succ){
            DB::commit();
            return AjaxResponse::isSuccess(__('auth.operationSuccess'));
        }else{
            DB::rollBack();
            return AjaxResponse::isFailure(__('auth.operationFailed'));
        }


    }

    /**
     * pda还柜完结显示页面
     * @author zt7242
     * @date 2019/5/7 9:39
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cabinet()
    {
        return view('pda.cabinet');
    }

    /**
     * 还柜完结提交
     * @author zt7242
     * @date 2019/5/8 13:53
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cabinetSumbit(Request $request)
    {
        $files = $request->input('filesInfo')??'';
        $data = $request->input('info');
        $cabinet_id = $data['cabinetId'];
        $actual_return_time = $data['actual_return_time'];

        $exitsWarehouse = ReturnCabinetService::warehouseExistByCabinetId($cabinet_id);

        if(!$exitsWarehouse){
            return AjaxResponse::isFailure(__('auth.PleaseCorrespondingWarehouse'));
        }

        if (empty($files)) {
            return AjaxResponse::isFailure(__('auth.PleaseUploadImage'));
        }
        if (empty($cabinet_id)) {
            return AjaxResponse::isFailure(__('auth.DidNotFindTheCounter'));
        }
        if (empty($actual_return_time)) {
            return AjaxResponse::isFailure(__('auth.ActualReturnTimeIsRequired'));
        }
        //更新还柜单信息、插入还柜附件、插入还柜日志
        $cabinet_ids = explode(',',$cabinet_id);
        DB::beginTransaction();

        $succ = 0;
        foreach($cabinet_ids as $cabinet_id){
            $res = ReturnCabinetService::updateAndInsertCabinetInfo($files,$cabinet_id,$actual_return_time);
            if($res){
                $succ++;
            }

        }

        if(count($cabinet_ids) == $succ){
            DB::commit();
            return AjaxResponse::isSuccess(__('auth.operationSuccess'));
        }else{
            DB::rollBack();
            return AjaxResponse::isFailure(__('auth.operationFailed'));
        }



    }
}
