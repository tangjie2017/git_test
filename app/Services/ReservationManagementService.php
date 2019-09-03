<?php

namespace App\Services;

use App\Models\ReservationManagement;
use App\Models\StaticState;
use App\Auth\Common\CurrentUser;
use App\Models\Warehouse;

class ReservationManagementService
{

    /**
     * 获取系统
     * @author zt7239
     * @return array
     */
    public static function getSystem($key = null)
    {
        $data = [
//            StaticState::SYSTEM_BIS => 'BIS',
            StaticState::SYSTEM_GC_OMS => 'GC-OMS',
//            StaticState::SYSTEM_EL_OMS => 'EL-OMS',
//            StaticState::SYSTEM_AE_OMS => 'AE-OMS',
        ];

        if ($key) {
            return $data[$key];
        }

        return $data;

    }

    /**
     * 目前只对接这两个仓库
     * @author zt7239
     * @param null $key
     * @return array|mixed
     */
    public static function getWarehouse($key = null)
    {
        $data = [
           
            StaticState::WAREHOUSE_USWE => __("auth.USWestWarehouse"),
            StaticState::WAREHOUSE_USEA => __("auth.USEastWarehouse")
        ];

        if ($key) {
            if(isset($data[$key])){
                return $data[$key];
            }
        }

        return $data;

    }

    /**
     * 获取柜型
     * @author zt7239
     * @param null $key
     * @return array
     */
    public static function getCabinetType($key=null)
    {
        $data = [
            StaticState::CABINET_TYPE_20GP => '20GP',
            StaticState::CABINET_TYPE_40GP => '40GP',
            StaticState::CABINET_TYPE_40HQ => '40HQ',
            StaticState::CABINET_TYPE_45HQ => '45HQ',
        ];

        if ($key) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * 获取类型
     * @author zt7239
     * @param null $key
     * @return array|mixed
     */
    public static function getType($key=null)
    {
        $data = [
            StaticState::TYPE_TIME_CABINET => __('auth.TimeLimitedCabinet'),
            StaticState::TYPE_NON_TIME_CABINET => __('auth.NonTimeLimitedCabinet'),
        ];

        if ($key) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * 获取来源
     * @author zt7239
     * @param null $key
     * @return array
     */
    public static function getSource($key=null)
    {
        $data = [
            StaticState::SOURCE_CLIENT => __('auth.client'),
            StaticState::SOURCE_WAREHOUSE => __('auth.warehouse')
        ];

        if ($key) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * 获取预约状态
     * @author zt7239
     * @param null $key
     * @return array
     */
    public static function getReservationStatus($key =null)
    {
        $data = [
            StaticState::RESERVATION_STATUS_NOT_EFFECTIVE => __('auth.NotActive'),
            StaticState::RESERVATION_STATUS_EFFECTIVE => __('auth.Effective'),
            StaticState::RESERVATION_STATUS_EXPIRED => __('auth.expired'),
            StaticState::RESERVATION_STATUS_END => __('auth.end'),
        ];

        if ($key) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * 获取预约主状态
     * @author zt7239
     * @param null $key
     * @return array
     */
    public static function getStatus($key = null)
    {
        $data = [
            StaticState::STATUS_DRAFT => __('auth.draft'),
            StaticState::STATUS_WAIT_RESERVATION => __('auth.PendingAppointment'),
            StaticState::STATUS_WAIT_APPROVAL => __('auth.Pending'),
            StaticState::STATUS_WAIT_SEND_WAREHOUSE => __('auth.WaitingForDelivery'),
            StaticState::STATUS_HAS_ARRIVED => __('auth.HasArrived'),
            StaticState::STATUS_DISCARD => __('auth.Discard'),
        ];

        if ($key) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * 获取货柜类型
     * @author zt7239
     * @param null $key
     * @return array
     */
    public static function getContainerType($key = null)
    {
        $data = [
            StaticState::CONTAINER_TYPE_ORDINARY => __('auth.ordinary'),
            StaticState::CONTAINER_TYPE_CABINET => __('auth.Cabinet'),
            StaticState::CONTAINER_TYPE_TO_FBA => __('auth.TransferToFBA'),
            StaticState::CONTAINER_TYPE_PART_TO_FBA => __('auth.PartiallyTransferredToFBA')
        ];

        if ($key) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * 获取操作类型
     * @author zt7239
     * @param null $key
     * @return array|mixed
     */
    public static function getOperationType($key = null)
    {
        $data = [
            StaticState::OPERATOR_TYPE_ADD => __('auth.add'),
            StaticState::OPERATOR_TYPE_EDIT => __('auth.edit')
        ];

        if ($key) {
            return $data[$key];
        }

        return $data;
    }


    public static function getReservationInfoById($reservation_number_id)
    {
        if(empty($reservation_number_id)) return false;
        return ReservationManagement::getReservationInfoById($reservation_number_id);
    }

    /**
     * 审核预约单
     * @author zt7239
     * @param $request
     * @return bool
     */
    public static function updateReview($request)
    {
        return ReservationManagement::updateReview($request);
    }

    /**
     * 首页统计
     * @author zt7242
     * @date 2019/5/13 20:09
     * @return array
     */
    public static function indexStatistiacl()
    {
        //折线图统计
        //取最近一周的时间
        $start_time = date('Y-m-d H:i:s',strtotime("-1 week"));

        $start_zone = Warehouse::switchTimeByZone($start_time);
        $start_time = Warehouse::opreationTimeZone($start_time);//zt3361 时区换算

        $end_time = date('Y-m-d H:i:s');

        $end_zone = Warehouse::switchTimeByZone($end_time);
        $end_time = Warehouse::opreationTimeZone($end_time);//zt3361 时区换算




        //默认预约单状态为已生效状态
        $data['reservation_status'] = StaticState::RESERVATION_STATUS_EFFECTIVE;
        $dataBar = [];
        $timeBar = [];

        //选择时间在一周内以天为维度统计
        $chart = ReservationManagement::getChartInfoByDay($data,$start_time,$end_time,$start_zone,$end_zone);

        foreach($chart as $key => $value){
            $dataBar[] = (int)$value->count;
            $timeBar[] = $value->created_time;
        }
        //左上边汇总统计
        $res = ReservationManagement::getStatistiaclInfo($data,$start_time,$end_time);
        $reservation = 0;
        $box = 0;
        $sku = 0;
        $product = 0;
        foreach($res as $k =>$v){
            $reservation += $v->reservation_num;
            $box += $v->sum_box;
            $sku += $v->sum_sku;
            $product += $v->sum_product;
        }
        return [
            'reservation'=>$reservation,
            'box'=>$box,
            'sku'=>$sku,
            'product'=>$product,
            'dataBar'=> json_encode($dataBar),
            'timeBar'=> json_encode($timeBar),
        ];
    }

    /**
     * 入库单统计
     * @author zt7242
     * @date 2019/5/13 19:57
     * @param $data
     * @param int $limit
     * @param $current_page
     * @return array
     */
    public static function getStatistiaclInfo($data, $limit = 10, $current_page)
    {
        //折线图统计
        if(isset($data['time_during'])){
            $start_zone = $start_time = substr($data['time_during'],0,19);
            $end_zone = $end_time = substr($data['time_during'],22);
        }

        if(!isset($start_time)){
            $start_time = date('Y-m-d H:i:s',strtotime("-1 week"));
            $start_zone = Warehouse::switchTimeByZone($start_time);
            $start_time = Warehouse::opreationTimeZone($start_time); //zt3361 时区换算
        }
        if(!isset($end_time)){
            $end_time = date('Y-m-d H:i:s');
            $end_zone = Warehouse::switchTimeByZone($end_time);
            $end_time = Warehouse::opreationTimeZone($end_time); //zt3361 时区换算
        }

        $start_unix = strtotime($start_time);
        $end_unix = strtotime($end_time);
        $diff = $end_unix - $start_unix;
        $dataBar = [];
        $timeBar = [];

        if($diff <= 7*24*3600){
//            //选择时间在一周内以天为维度统计

            $chart = ReservationManagement::getChartInfoByDay($data,$start_time,$end_time,$start_zone,$end_zone);
            foreach($chart as $key => $value){
                $dataBar[] = (int)$value->count;
                $timeBar[] = $value->created_time;
            }
        }elseif($diff <= 3*7*24*3600){
            //选择时间在三周内以两天为维度统计
            $date =[];
            $newDate1 =[];
            $newDate2 =[];
            $newDate3 =[];
            $dt_start = strtotime($start_time);
            $dt_end = strtotime($end_time);
            while ($dt_start<=$dt_end){
                $date[] = date('Y-m-d',$dt_start);
                $dt_start = strtotime('+1 day',$dt_start);
            }

            $date = array_flip($date);
            $chart = ReservationManagement::getChartInfoByTwoDay($data,$start_time,$end_time);
            foreach($chart as $key=>$value){
                $newDate1[$value->created_time] = (int)$value->count;
            }

            foreach($date as $k => $v){
                if(isset($newDate1[$k])){
                    $newDate2[$k] = $newDate1[$k];
                }else{
                    $newDate2[$k] = 0;
                }
            }
            $newDate2 = array_chunk($newDate2,2,true);
            foreach($newDate2 as $k=>$v){
                $kArray = array_keys($v);
                $newDate3[$kArray[0]] = array_sum($v);
            }
            $dataBar = array_values($newDate3);
            $timeBar = array_keys($newDate3);
        }else{
            //选择时间大于三周以周为维度统计
            $chart = ReservationManagement::getChartInfoByweek($data,$start_time,$end_time);
            foreach($chart as $key => $value){
                $dataBar[] = (int)$value->count;
                $timeBar[] = $value->created_time;
            }

        }
        //列表统计
        $res = ReservationManagement::getStatistiaclInfo($data,$start_time,$end_time);
        $reservation = 0;
        $box = 0;
        $sku = 0;
        $product = 0;
        foreach($res as $k =>$v){
            $reservation += $v->reservation_num;
            $box += $v->sum_box;
            $sku += $v->sum_sku;
            $product += $v->sum_product;
        }
        $item = array_slice($res,($current_page-1)*$limit,$limit);
        $total = count($res);
        return [
            'count'=>$total,
            'item'=>$item,
            'reservation'=>$reservation,
            'box'=>$box,
            'sku'=>$sku,
            'product'=>$product,
            'dataBar'=> json_encode($dataBar),
            'timeBar'=> json_encode($timeBar),

        ];
    }
    /**
     * 通过单号查询预约单号或海柜号是否存在
     * @author zt7242
     * @date 2019/5/7 13:12
     * @param $num
     * @return bool
     */
    public static function ReservationOrArknumberExist($num)
    {
        if(empty($num)) return false;
        return ReservationManagement::ReservationOrArknumberExist($num);
    }

    /**
     * 判读单号是否属于选择的仓库或绑定仓库
     * @author zt7239
     * @param $num
     * @return bool
     */
    public static function warehouseExist($num)
    {
        if(empty($num)) return false;
        return ReservationManagement::warehouseExist($num);
    }

    /**
     * 提交的时候，判读预约单id是否属于选择的仓库或绑定仓库
     * @author zt7239
     * @param $id
     * @return bool
     */
    public static function warehouseExistByReservationId($id)
    {
        if(empty($id)) return false;
        return ReservationManagement::warehouseExistByReservationId($id);
    }

    /**
     * 更新预约单信息及插入还柜附件
     * @author zt7242
     * @date 2019/5/8 16:15
     * @param $files
     * @param $reservation_number_id
     * @param $actual_arrival_time
     * @return bool
     */
    public static function updateAndInsertReservationInfo($files,$reservation_number_id,$actual_arrival_time)
    {
        $time = date('Y-m-d H:i:s');
        $currentUser = CurrentUser::getCurrentUser();
        //通过预约单号获取预约信息
        $reservationInfo = ReservationManagement::find($reservation_number_id);

        if(empty($reservationInfo)){
            return false;
        }elseif ($reservationInfo->status == StaticState::STATUS_HAS_ARRIVED && $reservationInfo->reservation_status == StaticState::RESERVATION_STATUS_END){
            //预约单状态为已送仓，预约状态变已完结
            return false;
        }

        //组装还柜数据
        $cabinet['reservation_number_id'] = $reservation_number_id;
        $cabinet['system'] = $reservationInfo->system;
        $cabinet['warehouse_code'] = $reservationInfo->warehouse_code;
        $cabinet['warehouse_name'] = $reservationInfo->warehouse_name;
        $cabinet['cabinet_type'] = $reservationInfo->cabinet_type;
        $cabinet['operating_time'] = $time;
        $cabinet['operator'] = $currentUser->userCode??'无';
        $cabinet['source'] = $reservationInfo->source;
        $cabinet['status'] = StaticState::RETURN_STATUS_UNLOADING;
        $cabinet['created_at'] = $time;
        $cabinet['updated_at'] = $time;

        return ReservationManagement::updateAndInsertReservationInfo($files,$cabinet,$reservation_number_id,$actual_arrival_time);
    }

    /**
     * 更新预约单邮件信息
     * @author zt7242
     * @date 2019/4/29 17:03
     * @param $time
     * @param $ids
     * @return mixed
     */
    public static function updateEmailInfo($time,$ids)
    {
        return ReservationManagement::updateEmailInfo($time,$ids);
    }


    /**
     * 查询满足条件预约单邮箱
     * @author zt7242
     * @date 2019/4/29 16:12
     * @param $condition
     * @return mixed
     */
    public static function getReservationEmailByCondition($condition)
    {
        return ReservationManagement::getReservationEmailByCondition($condition);
    }

    /**
     * 导出预约单信息
     * @author zt7242
     * @date 2019/4/24 17:24
     * @param $condition
     * @return int
     */
    public static function exportReservationInfoByCondition($condition)
    {
        return ReservationManagement::exportReservationInfoByCondition($condition);
    }

    /**
     * 根据预约单id查询预约单及入库单信息
     * @author zt7242
     * @date 2019/5/17 9:56
     * @param $id
     * @return ReservationManagement[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getResercationInfoById($id)
    {
        return ReservationManagement::getResercationInfoById($id);
    }

    /**
     * 创建预约单保存
     * @author zt7239
     * @param int $rm_id
     * @param array $requestData
     * @return bool
     */
    public static function saveData($rm_id,$requestData,$inbound_order)
    {
        $nowTime = date('Y-m-d H:i:s');

        $rmData = [
            'system'=>$requestData['system'],
            'source' => StaticState::SOURCE_WAREHOUSE,
            'warehouse_code'=>$requestData['warehouse_code'],
            'warehouse_name'=>self::getWarehouse($requestData['warehouse_code']),//前期只有美西和美东
            'type'=>$requestData['type'],
            'cabinet_type'=>$requestData['cabinet_type'],
            'file'=>isset($requestData['file']) && $requestData['file'] ? $requestData['file'] : '',
            'container_type'=>$requestData['container_type'],
            'customs_clearance_time'=>Warehouse::switchWareTime($requestData['customs_clearance_time']),
            'arrival_time'=>Warehouse::switchWareTime($requestData['arrival_time']),
            'earliest_delivery_time'=>Warehouse::switchWareTime($requestData['earliest_delivery_time']),
            'latest_delivery_time'=>Warehouse::switchWareTime($requestData['latest_delivery_time']),
            'contact_name'=>$requestData['contact_name'],
            'email'=>$requestData['email'],
            'telephone'=>$requestData['telephone'],
            'operator'=>CurrentUser::getCurrentUser()->userCode,
            'operating_time'=>Warehouse::switchWareTime($nowTime),
        ];
        $inboundOrderData = [];
        if($inbound_order){
            foreach($inbound_order as $k=>$v){
                $inboundOrderData[] = [
                    'inbound_order_number' => $v['inbound_order_number'],
                    'tracking_number' => $v['tracking_number'],
                    'sea_cabinet_number' => isset($v['sea_cabinet_number']) ? $v['sea_cabinet_number'] : '',
                    'customer_code' => $v['customer_code'],
                    'warehouse_code' => $v['warehouse_code'],
                    'warehouse_name' => $v['warehouse_name'],
                    'products_number' => $v['products_number'],
                    'box_number' => $v['box_number'],
                    'sku_species_number' => isset($v['sku_species_number'])?$v['sku_species_number']:0,
                    'weight' => $v['weight'],
                    'volume' => $v['volume'],
                    'created_at' =>$v['created_at'],
                ];
            }
        }

        return ReservationManagement::saveData($rm_id,$rmData,$requestData,$inboundOrderData);

    }


    /**
     * 获取预约单列表
     * @author zt7239
     * @param $serData
     * @param $limit
     * @return array
     */
    public static function getDataByFilter($serData,$limit)
    {
        return ReservationManagement::getDataByFilter($serData,$limit);
    }

    /**
     * 废弃预约单
     * @author zt7239
     * @param $id
     * @return bool
     */
    public static function discard($id)
    {
        return ReservationManagement::discard($id);
    }

    /**
     * 预约审核
     * @author zt7239
     * @param $id
     * @return bool
     */
    public static function appointmentReview($id)
    {
        return ReservationManagement::appointmentReview($id);
    }

    /**
     * 获取详情
     * @author zt7239
     * @param $id
     * @return mixed
     */
    public static function getDetail($id)
    {
        return ReservationManagement::getDetail($id);
    }

    /**
     * 获取预约单的附件
     * @author zt7239
     * @param $id
     * @return mixed
     */
    public static function getFile($id)
    {
        return ReservationManagement::getFile($id);
    }

    /**
     * 获取日志
     * @author zt7239
     * @param $id
     * @return mixed
     */
    public static function getLog($id)
    {
        return ReservationManagement::getLog($id);
    }

    /**
     * 校验入库单是否已创建
     * @author zt7239
     * @param array $inbound_order
     * @param int $rm_id
     * @return array
     */
    public static function validaterData($inbound_order = array(),$rm_id =0,$warehouse_code ='')
    {
        return ReservationManagement::validaterData($inbound_order,$rm_id,$warehouse_code);
    }

}