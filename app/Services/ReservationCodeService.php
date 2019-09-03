<?php
/**
 * @author zt12700
 * CreateTime: 2019/5/6 13:29
 *
 */

namespace App\Services;

use App\Auth\Common\Response;
use App\Auth\Common\AjaxResponse;
use App\Models\ReservationManagement;
use App\Models\Warehouse;
use App\Models\ReservationManagementLog;
use App\Auth\Common\CurrentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Common\Aes;

class ReservationCodeService
{
    public static function search($code){
        $res = ReservationManagement::where('reservation_code',$code)->first();

        if(collect($res)->isNotEmpty()){
            $res = $res->toArray();

            //判断状态是否为待预约状态，待送仓状态，请输入有效的预约码
           if($res['status'] !=2 && $res['status'] !=4){
               return AjaxResponse::isFailure(__('auth.EnterCorrectReservationCode'));
           }
           //判断待预约状态下预约状态是否为未生效状态
           if($res['status'] ==2 && $res['reservation_status'] != 1){
               return AjaxResponse::isFailure(__('auth.NoReservationsAllowed'));
           }
            //判断待送仓状态下预约状态是否为为已过期
            if($res['status'] ==4 && $res['reservation_status'] != 3){
                return AjaxResponse::isFailure(__('auth.ReservationFormEffect'));
            }

            $res['reservation_number_id'] = encrypt($res['reservation_number_id']);
            $data = [
                'Status'=>1,
                'res'=>$res
            ];
            return Response()->json($data);
        }else{
            return AjaxResponse::isFailure(__('auth.EnterCorrectReservationCode'));
        }

    }

    /**
     * 更改供应商信息
     * @author zt12700
     * @param $requestData
     */
    public static function updateData($request){
        $id = $request->id;
//        dd($request->id);
        DB::beginTransaction();
        try{
            $reservation_management = ReservationManagement::find($id);
            if($reservation_management->status == 3){
                return AjaxResponse::isFailure(__('auth.ReservationChanged'));
            }

            //目的仓
            $DestinationWarehouse = $reservation_management->warehouse_code;
            if($DestinationWarehouse == 'USEA'){
                $timeZone = 12;
            }else{
                $timeZone = 15;
            }

            $reservation_management->appointment_delivery_time =  date('Y-m-d H:i:s',strtotime($request->appointment_delivery_time)+$timeZone*3600);
            $reservation_management->contact_name = $request->contact_name;
            $reservation_management->telephone =$request->telephone;
            $reservation_management->email = $request->email;
            $res = $reservation_management->save();
            if($res){
                $reservation_management->status=3;
                $reservation_management->reservation_status=1;
                $reservation_management->reservation_num = $reservation_management->reservation_num+1;
                $result = $reservation_management->save();
            }

            //判断是否登录
            $currentUser = CurrentUser::getCurrentUser();
            if($currentUser){
                $reservation_management_log = new ReservationManagementLog();
                $reservation_management_log->reservation_number_id = $id;
                $reservation_management_log->operator_user_id = $currentUser->userId;
                $reservation_management_log->operator_user_name = $currentUser->userCode;
                $reservation_management_log->operator_type = 2;
                $reservation_management_log->operator_time = Warehouse::opreationTimeZone(date('Y-m-d H:i:s'));
                $reservation_management_log->content = '预约送仓编辑';
                $reservation_management_log->save();

            }else{
                $content = $request->session()->get('content');
                if($content == null){
                    return AjaxResponse::isFailure(__('auth.LogAndRetry'));
                }else{
                    $aes = new Aes(Aes::getDefaultKey());
                    $requestData = $aes->decrypt($content);
                    if($requestData){
                        $requestData = json_decode($requestData,true);
                        $reservation_management_log = new ReservationManagementLog();
                        $reservation_management_log->reservation_number_id = $id;
                        $reservation_management_log->operator_user_id = $requestData['user_id'];
                        $reservation_management_log->operator_user_name = $requestData['user_code'];
                        $reservation_management_log->operator_type = 2;
                        $reservation_management_log->operator_time = Warehouse::opreationTimeZone(date('Y-m-d H-i-s'));
                        $reservation_management_log->content = '预约送仓编辑';
                        $reservation_management_log->save();
                    }else{
                        return AjaxResponse::isFailure(__('auh.NecessaryParameters'));
                    }
                }
            }

            DB::commit();
            return AjaxResponse::isSuccess(__('auth.PositionSuccessfully'));
        }catch (\PDOException $exception){
            Log::info($exception->getMessage());
            DB::rollBack();
            return AjaxResponse::isFailure(__('auth.PositionSuccessfully'));
        }
    }
}