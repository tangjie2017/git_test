<?php
namespace App\Http\Timer;

use App\Common\Excels;
use App\Http\Controllers\MailController;
use App\Models\Download;
use App\Models\DownloadCleanup;
use App\Models\DownloadTimer;
use App\Models\StaticState;
use App\Services\DownloadService;
use App\Services\DownloadTimerService;
use App\Services\ReservationManagementService;
use App\Services\ReturnCabinetService;
use App\Services\TaskConfigService;
use App\Models\ConsigneeNoticeList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
/**
 * 定时器方法
 */
class TimeRun
{

    /**
     * 定时清除下载数据
     * @author zt7242
     * @date 2019/5/9 9:31
     */
    public static function cleanupTimer()
    {
        //通过数据库查询清除周期
        $cycle = DownloadCleanup::get()->first();
        if(empty($cycle)){
            log::info('没有设置清除周期');
            exit();
        }
        $cleanup_cycle = $cycle->cleanup_cycle;
        switch($cleanup_cycle){
            case 1:
              $day = 3;
              break;
            case 2:
                $day = 7;
                break;
            case 3:
                $day = 15;
                break;
            default:
                $day = 30;
        }

        $now = date("Y-m-d");
        $now_unix = strtotime($now);
        $time_unix = $now_unix-$day*3600*24;
        $time= date("Y-m-d 23:59:59",$time_unix);
        //删除$day天以前的下载数据
        DB::beginTransaction();
        try{
            Download::where('created_at','<',$time)->delete();
            DownloadTimer::where('created_at','<',$time)->delete();
            //TODO 刪除服务器上的文件

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            log::info('清除周期：清除周期失败,'.$e);
        }



    }

    /**
     * 邮件发送定时器
     * @author zt7242
     * @date 2019/5/8 16:44
     */
    public static function mailSendTimer()
    {
        //获取通知配置信息
        $config = TaskConfigService::getConfigInfo();

        if(!isset($config)){
            log::info('邮件发送:暂无配置');
            exit();
        }
        $condition = $config->toArray();
        //获取通知收件人信息
        $mailConsignee = ConsigneeNoticeList::getEmail();

        if(empty($config->is_notice_open) || empty($config->email_open)){
            log::info('邮件发送:配置未开启');
            exit();
        }
        //根据配置条件查询满足条件的预约单邮箱
        $emailData = ReservationManagementService::getReservationEmailByCondition($condition);
        if(empty($emailData) || (empty($config->email_notice_supplier) && empty($mailConsignee))){
            log::info('邮件发送:没有可通知的对象');
            exit();
        }

        //数据组装
        $newData = [];
        if(!empty($config->email_notice_supplier)){
            foreach($emailData as $k1 => $v1){
                $newData[$k1][] = $v1;
                if($mailConsignee){
                    foreach($mailConsignee as $k2 =>$v2){
                        $newData[$k1][] = $v2 ;
                    }

                }
            }
        }else{
            foreach($emailData as $k1 => $v1){
                if($mailConsignee){
                    foreach($mailConsignee as $k2 =>$v2){
                        $newData[$k1][] = $v2 ;
                    }

                }
            }

        }
        //邮件通知时间
        $time = date('Y-m-d H:i:s');
        //邮件内容
        $content = $config->email_notice_content;
        //邮件标题
        $subject = '预约系统通知';
        $mailObject = new MailController();
        $ids = [];

        foreach($newData as $k3 => $v3){
            //获取预约单的预约码及海柜号
            $res = ReservationManagementService::getResercationInfoById($k3);
            //预约码
            $reservation_code = isset($res['reservation_code']) ? $res['reservation_code'] : '';
            //海柜号
            $sea = '';

            if(!isset($res['InboundOrder'])) continue;
            foreach($res['InboundOrder'] as $item){
                $sea .= $item['tracking_number'].';' ;
            }

            $cont = 'Reservation code：'.$reservation_code.'，Sea cabinet number：'.$sea.$content;
            foreach($v3 as $k4 => $v4){
                try{
                    $mailObject->send($cont,$v4,$subject);

                }catch(\Exception $e){
                    log::info('邮件发送:'.$v4.'邮件发送失败'.$e);
                    continue ;
                }
            }
            if(is_int($k3)){
                //如果是整型说明是预约单id
                $ids[] = $k3;
            }

        }

        if($ids){
            $stringIds = implode(",",$ids);
        }else{
            log::info('邮件发送:邮件发送失败或没有可通知的供应商');
            exit();
        }
        //更新预约单的email_reminder_time和email_reminder_number
        $update = ReservationManagementService::updateEmailInfo($time,$stringIds);

        if($update){
            log::info('邮件发送:预约单邮件提醒时间和次数更新成功！');
        }else{
            log::info('邮件发送:预约单邮件提醒时间和次数更新失败！');
        }


    }

    /**
     * 下载定时器
     * @author zt7242
     * @date 2019/4/28 17:41
     */
    public static function downloadTimer()
    {
        try{
            //获取下载条件及下载目录,一次20条
            $limit = 20;
            $info = DownloadTimerService::getDownloadTimerInfo($limit);
            if(empty($info)){
                log::info('下载任务:没有需要下载的数据！');
                exit();
            }
            foreach($info as $k => $v){
                $condition = unserialize($v['download_condition']);
                $menu_id = $v['menu_id'];

                //导出预约单信息
                if($menu_id == 1){
                    $reservation = ReservationManagementService::exportReservationInfoByCondition($condition);

                    if($reservation->isEmpty()){
                        DownloadService::updateFileAndStatus('',$v['download_id']);
                        continue;
                    }
                    //将查询数据转数组
                    $reservationArr = $reservation->toArray();
                    //将数据导入excel表
                    $file_name = self::exportAndStoreReservation($reservationArr);
                    $filePath = $file_name ?'storage/exports/'.date('Ymd').'/'.$file_name.'.xlsx' : null;
                }else{
                    //导出还柜信息
                    $return = ReturnCabinetService::exportReturnCabinetInfoByCondition($condition);
                    if($return->isEmpty()){
                        //修改dowmload表状态为失败
                        DownloadService::updateFileAndStatus('',$v['download_id']);
                        continue;
                    }
                    //将查询数据转数组
                    $returnArr = $return->toArray();
                    //将数据导入excel表
                    $file_name = self::exportAndStoreReturnCabinet($returnArr);
                    $filePath = $file_name ? 'storage/exports/'.date('Ymd').'/'.$file_name.'.xlsx' : null;

                }
                //将下载地址保存download表中并修改dowmload表状态
                $res = DownloadService::updateFileAndStatus($filePath,$v['download_id']);
                if(!$res){
                    log::info('下载任务:'.$v['download_id'].'下载失败！');
                }else{
                    log::info('下载任务:'.$v['download_id'].'下载成功！');
                }

            }
        }catch (\Exception $e){
            log::info('下载任务异常'.$e);
        }

    }

    /**
     * 生成预约excel
     * @author zt7242
     * @date 2019/4/25 13:35
     * @param $data
     * @return bool|string
     */
    public static function exportAndStoreReservation($data)
    {
        try{
            $header = ['预约单号','预约码','入库单号/跟踪号','系统','仓库','客户代码','柜型','产品数','最早提货时间','最晚提货时间',
                '预约递送时间','实际到仓时间','剩余天数','预约状态','预约次数','操作时间','操作人','来源'];
            $print = [] ;
            $time = time();
            foreach($data as $k => $v){
                //获取剩余时间
                if(!empty($v['appointment_delivery_time'])){
                    $appointment_time = strtotime($v['appointment_delivery_time']);
                    $rest = ceil(($appointment_time - $time)/3600/24);
                }else{
                    $rest = '';
                }


                $inbound = '';
                $customerCode = '';
                $productsNumber = 0;
                foreach ($v['inbound_order'] as $key => $value){
                    $inbound .= $value['inbound_order_number']."/".$value['tracking_number']."\r\n";
                    $customerCode .= $value['customer_code']."\r\n";
                    $productsNumber += $value['products_number'];

                }
                $print[$k][] = $v['reservation_number'];
                $print[$k][] = $v['reservation_code'];

                $print[$k][] = $inbound;
                switch ($v['system']){
                    case 1:
                        $print[$k][] = 'BIS';
                        break;
                    case 2:
                        $print[$k][] = 'GC-OMS';
                        break;
                    case 3:
                        $print[$k][] = 'EL-OMS';
                        break;
                    default:
                        $print[$k][] = 'AE-OMS';
                }
                $print[$k][] = $v['warehouse_name'];
                $print[$k][] = $customerCode;
                switch ($v['cabinet_type']){
                    case 1:
                        $print[$k][] = '20GP';
                        break;
                    case 2:
                        $print[$k][] = '40GP';
                        break;
                    case 3:
                        $print[$k][] = '40HQ';
                        break;
                    default:
                        $print[$k][] = '45HQ';
                }
                $print[$k][] = $productsNumber;
                $print[$k][] = $v['earliest_delivery_time'];
                $print[$k][] = $v['latest_delivery_time'];
                $print[$k][] = $v['appointment_delivery_time'];
                $print[$k][] = $v['actual_arrival_time'];
                //剩余天数
                $print[$k][] = $rest;
                switch ($v['reservation_status']){
                    case 1:
                        $print[$k][] = '未生效';
                        break;
                    case 2:
                        $print[$k][] = '已生效';
                        break;
                    case 3:
                        $print[$k][] = '已过期';
                        break;
                    default:
                        $print[$k][] = '完结';
                }
                //预约次数
                $print[$k][] = $v['reservation_num'];
                //操作时间
                $print[$k][] = $v['operating_time'];
                //操作人
                $print[$k][] = $v['operator'];
                switch ($v['source']){
                    case 1:
                        $print[$k][] = '客户';
                        break;
                    default:
                        $print[$k][] = '仓库';
                }

            }
            array_unshift($print ,$header) ;

            $file_name = date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            Excels::store($print,$file_name);
            return $file_name;
        }catch (\Exception $e){
            log::info('预约excel:excel上传服务器异常'.$e);
            return false;
        }

    }

    /**
     * 生成还柜excel
     * @author zt7242
     * @date 2019/4/25 13:35
     * @param $data
     * @return bool|string
     */
    public static function exportAndStoreReturnCabinet($data)
    {
        try{
            $header = ['预约单号','入库单号/跟踪号','系统','仓库','客户代码','柜型','实际卸货开始','实际卸货结束',
                '实际还柜时间','通知还柜时间','操作时间','操作人','来源','状态'];
            $print = [] ;
            foreach($data as $k => $v){
                $inbound = '';
                $customerCode = '';
                foreach ($v['inbound'] as $key => $value){
                    $inbound .= $value['inbound_order_number']."/".$value['tracking_number']."\r\n";
                    $customerCode .= $value['customer_code']."\r\n";

                }

                $print[$k][] = $v['rem']['reservation_number'];
                $print[$k][] = $inbound;
                switch ($v['rem']['system']){
                    case 1:
                        $print[$k][] = 'BIS';
                        break;
                    case 2:
                        $print[$k][] = 'GC-OMS';
                        break;
                    case 3:
                        $print[$k][] = 'EL-OMS';
                        break;
                    default:
                        $print[$k][] = 'AE-OMS';
                }
                $print[$k][] = $v['warehouse_name'];
                $print[$k][] = $customerCode;
                switch ($v['cabinet_type']){
                    case 1:
                        $print[$k][] = '20GP';
                        break;
                    case 2:
                        $print[$k][] = '40GP';
                        break;
                    case 3:
                        $print[$k][] = '40HQ';
                        break;
                    default:
                        $print[$k][] = '45HQ';
                        break;
                }
                $print[$k][] = $v['actual_start_time'];
                $print[$k][] = $v['actual_end_time'];
                $print[$k][] = $v['actual_return_time'];
                $print[$k][] = $v['notice_return_time'];
                //操作时间
                $print[$k][] = $v['operating_time'];
                //操作人
                $print[$k][] = $v['operator'];
                switch ($v['source']){
                    case 1:
                        $print[$k][] = '客户';
                        break;
                    default:
                        $print[$k][] = '仓库';
                }
                switch ($v['status']){
                    case StaticState::RETURN_STATUS_UNLOADING:
                        $print[$k][] = '待卸柜';
                        break;
                    case StaticState::RETURN_STATUS_ALREADY:
                        $print[$k][] = '已卸柜';
                        break;
                    case StaticState::RETURN_STATUS_RETURN_UNLOADING:
                        $print[$k][] = '待还柜';
                        break;
                    default:
                        $print[$k][] = '已还柜';
                        break;
                }


            }
            array_unshift($print ,$header) ;

            $file_name = date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            Excels::store($print,$file_name);
            return $file_name;
        }catch (\Exception $e){
            log::info('还柜excel:excel上传服务器异常'.$e);
            return false;
        }

    }
}