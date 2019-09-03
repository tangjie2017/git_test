<?php

namespace App\Models;

use App\Auth\Common\CurrentUser;
use App\Http\Api\OWMSService;
use App\Services\ReservationManagementService;
use function foo\func;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 路由模型
 */
class ReservationManagement extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'reservation_management';

    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'reservation_number_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 关联入库单表
     * @author zt7239
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function InboundOrder()
    {
        return $this->hasMany('App\Models\InboundOrder','reservation_number_id','reservation_number_id');
    }

    /**
     * 通过预约单id获取预约数据
     * @author zt7242
     * @date 2019/5/10 17:19
     * @param $reservation_number_id
     * @return mixed
     */
    public static function getReservationInfoById($reservation_number_id)
    {
        return self::find($reservation_number_id);
    }

    /**
     * 入库单统计列表统计
     * @author zt7242
     * @date 2019/5/13 17:17
     * @param $data
     * @param $start_time
     * @param $end_time
     * @return mixed
     */
    public static function getStatistiaclInfo($data,$start_time,$end_time)
    {
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime = $currentUser->wareTime;
        if($wareTime == '-12'){
            $wareCode = 'USEA';
        }else{
            $wareCode = 'USWE';
        }

        $where = "WHERE created_at > '".$start_time."' AND created_at < '".$end_time."'";

        $where .= " AND `warehouse_code` = '".$wareCode."'";

        if(isset($data['reservation_status'])) {
            $where .= " AND `reservation_status` = ".$data['reservation_status'];
        }

        $reservation = DB::select(
            "SELECT rm.warehouse_code,SUM(reservation_total) reservation_num, count(DISTINCT rm.cabinet_type) cabinet, SUM(box) sum_box,
             SUM(products) sum_product, SUM(sku) sum_sku, AVG(vo) volume, reservation_status, MIN(created_at) created_min, MAX(created_at) created_max
             FROM reservation_management AS rm 
             LEFT JOIN (
	            SELECT count(inbound_order_id) reservation_total,reservation_number_id,SUM(box_number) box,SUM(products_number) products,SUM(sku_species_number) sku,avg(volume) AS vo
	            FROM inbound_order GROUP BY reservation_number_id) AS io 
	         ON rm.reservation_number_id = io.reservation_number_id ".$where."
            GROUP BY rm.warehouse_code,rm.reservation_status"
        );
        return $reservation;
    }

    /**
     * 入库单统计折线图（以天为维度）
     * @author zt7242
     * @date 2019/5/13 9:52
     * @param $data
     * @param $start_time
     * @param $start_zone
     * @param $end_zone
     * @return ReservationManagement
     */
    public static function getChartInfoByDay($data,$start_time,$end_time,$start_zone,$end_zone)
    {
        $start = date('Y-m-d',strtotime($start_zone));
        $end = date('Y-m-d',strtotime($end_zone));
        $where1 = "WHERE created_time >= '".$start."' AND created_time <= '".$end."'";
        $where = "WHERE created_at > '".$start_time."' AND created_at < '".$end_time."'";
        if(isset($data['warehouse_code'])) {
            $where .= " AND `warehouse_code` = '".$data['warehouse_code']."'";
        }
        if(isset($data['reservation_status'])) {
            $where .= " AND `reservation_status` = ".$data['reservation_status'];
        }

        //按时区切换查询
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime =  $currentUser->wareTime;
        if ($wareTime== '-12'){
            $sql = "SELECT DATE(date_sub(created_at,INTERVAL 12 HOUR )) AS datetime,SUM(reservation_total)  count FROM reservation_management AS rm";
        }elseif($wareTime== '-15'){
            $sql = "SELECT DATE(date_sub(created_at,INTERVAL 15 HOUR )) AS datetime,SUM(reservation_total)  count FROM reservation_management AS rm";
        }else{
            $sql = "SELECT DATE(created_at) AS datetime,SUM(reservation_total)  count FROM reservation_management AS rm";
        }




        $chart = DB::select(
            "SELECT a.created_time,ifnull(b.count,0) AS count FROM (
            SELECT DATE('$end') AS created_time
            UNION ALL
            SELECT date_sub(DATE('$end_zone'),INTERVAL 1 DAY) AS created_time
            UNION ALL
            SELECT date_sub(DATE('$end_zone'), INTERVAL 2 DAY) AS created_time
            UNION ALL
            SELECT date_sub(DATE('$end_zone'), INTERVAL 3 DAY) AS created_time
            UNION ALL
            SELECT date_sub(DATE('$end_zone'), INTERVAL 4 DAY) AS created_time
            UNION ALL
            SELECT date_sub(DATE('$end_zone'), INTERVAL 5 DAY) AS created_time
            UNION ALL
            SELECT date_sub(DATE('$end_zone'), INTERVAL 6 DAY) AS created_time
        ) a LEFT JOIN (
             ".$sql."
              LEFT JOIN (
             SELECT reservation_number_id,count(inbound_order_id) reservation_total FROM inbound_order GROUP BY reservation_number_id
                        ) AS io ON rm.reservation_number_id = io.reservation_number_id ".$where." GROUP BY datetime
        ) b ON a.created_time = b.datetime ".$where1." ORDER BY a.created_time ASC"
        );
        return $chart;
    }

    /**
     * 入库单统计折线图（以两天为维度）
     * @author zt7242
     * @date 2019/5/13 9:52
     * @param $data
     * @param $start_time
     * @param $end_time
     * @return ReservationManagement
     */
    public static function getChartInfoByTwoDay($data,$start_time,$end_time)
    {

        $where = "WHERE created_at >= '".$start_time."' AND created_at <= '".$end_time."'";
        if(isset($data['warehouse_code'])) {
            $where .= " AND `warehouse_code` = '".$data['warehouse_code']."'";
        }
        if(isset($data['reservation_status'])) {
            $where .= " AND `reservation_status` = ".$data['reservation_status'];
        }

        //按时区切换查询
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime =  $currentUser->wareTime;
        if ($wareTime== '-12'){
            $sql = " DATE(date_sub(created_at,INTERVAL 12 HOUR )) AS created_time,SUM(reservation_total) AS count FROM reservation_management AS rm";
        }elseif($wareTime== '-15'){
            $sql = " DATE(date_sub(created_at,INTERVAL 15 HOUR )) AS created_time,SUM(reservation_total) AS count FROM reservation_management AS rm";
        }else{
            $sql = " DATE(created_at) AS created_time,SUM(reservation_total) AS count FROM reservation_management AS rm";
        }

        $chart = DB::select(
            "SELECT ".$sql."
              LEFT JOIN (
             SELECT reservation_number_id,count(inbound_order_id) reservation_total FROM inbound_order GROUP BY reservation_number_id
                        ) AS io ON rm.reservation_number_id = io.reservation_number_id ".$where." GROUP BY created_time ORDER BY created_time ASC "
        );

        return $chart;

    }

    /**
     * 入库单统计折线图（以周为维度）
     * @author zt7242
     * @date 2019/5/13 9:52
     * @param $data
     * @param $start_time
     * @param $end_time
     * @return ReservationManagement
     */
    public static function getChartInfoByweek($data,$start_time,$end_time)
    {
        $where = "WHERE created_at >= '".$start_time."' AND created_at <= '".$end_time."'";
        if(isset($data['warehouse_code'])) {
            $where .= " AND `warehouse_code` = '".$data['warehouse_code']."'";
        }
        if(isset($data['reservation_status'])) {
            $where .= " AND `reservation_status` = ".$data['reservation_status'];
        }

        //按时区切换查询
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime =  $currentUser->wareTime;
        if ($wareTime== '-12'){
            $sql = " DATE_FORMAT(date_sub(created_at,INTERVAL 12 HOUR ),'%Y年第%v周') AS created_time,SUM(reservation_total) AS count FROM reservation_management AS rm";
        }elseif($wareTime== '-15'){
            $sql = " DATE_FORMAT(date_sub(created_at,INTERVAL 15 HOUR ),'%Y年第%v周') AS created_time,SUM(reservation_total) AS count FROM reservation_management AS rm";
        }else{
            $sql = " DATE_FORMAT(created_at,'%Y年第%v周') AS created_time,SUM(reservation_total) AS count FROM reservation_management AS rm";
        }

        $chart = DB::select(
            "SELECT ".$sql."
              LEFT JOIN (
             SELECT reservation_number_id,count(inbound_order_id) reservation_total FROM inbound_order GROUP BY reservation_number_id
                        ) AS io ON rm.reservation_number_id = io.reservation_number_id ".$where." GROUP BY created_time ORDER BY created_time ASC "
        );
        return $chart;
        
    }

    /**
     * 通过单号查询预约单号或海柜号是否存在
     * @author zt7242
     * @date 2019/5/7 13:13
     * @param $num
     * @return mixed
     */
    public static function ReservationOrArknumberExist($num)
    {

        $isExist = self::whereHas('InboundOrder', function ($query) use ($num) {
                $query->where('tracking_number',$num);
                $query->orwhere('sea_cabinet_number',$num);
            })
            ->orwhere('reservation_number',$num)
            ->select('reservation_number_id')
            ->get();
        return $isExist;
    }

    /**
     * 判读单号是否属于选择的仓库或绑定仓库
     * @author zt7239
     * @param $num
     * @return bool
     */
    public static function warehouseExist($num)
    {
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime = $currentUser->wareTime;
        if($wareTime == '-12'){
            $wareCode = 'USEA';
        }else{
            $wareCode = 'USWE';
        }


        $res = self::where('warehouse_code',$wareCode)->whereHas('InboundOrder', function ($query) use ($num,$wareCode) {
//            $query->where('inbound_order.warehouse_code',$wareCode);
            $query->where('tracking_number',$num);
            $query->orWhere('sea_cabinet_number',$num);
            $query->orWhere('reservation_number',$num);
        })->get();

        if($res->isEmpty()){
            return false;
        }else{
            return true;
        }

    }

    /**
     * 提交的时候，判读预约单id是否属于选择的仓库或绑定仓库
     * @author zt7239
     * @param $id
     * @return bool
     */
    public static function warehouseExistByReservationId($id)
    {
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime = $currentUser->wareTime;
        if($wareTime == '-12'){
            $wareCode = 'USEA';
        }else{
            $wareCode = 'USWE';
        }

        $res = self::where('warehouse_code',$wareCode)->where('reservation_number_id',$id)->first();

        if($res){
            return true;
        }else{
            return false;
        }

    }



    /**
     * 更新预约单信息及插入还柜附件、生成还柜单
     * @author zt7242
     * @date 2019/5/8 11:24
     * @param $path
     * @param $cabinet
     * @param $reservation_number_id
     * @param $actual_arrival_time
     * @return bool
     */
    public static function updateAndInsertReservationInfo($path,$cabinet,$reservation_number_id,$actual_arrival_time)
    {
        if(empty($cabinet)) return false;
        $time = date('Y-m-d H:i:s');

        try{
            //更新预约单状态变为已送仓，预约状态变为已完结
            $res1 = self::where('reservation_number_id',$reservation_number_id)->update(['status'=>StaticState::STATUS_HAS_ARRIVED,
                'reservation_status'=>StaticState::RESERVATION_STATUS_END,'actual_arrival_time'=>Warehouse::switchWareTime($actual_arrival_time),'updated_at' => $time]);

            //生成还柜单
            $return_cabinet_id = ReturnCabinet::insertGetId($cabinet);

            $currentUser = CurrentUser::getCurrentUser();
            //插入还柜日志
            if($return_cabinet_id){
                $logInfo['return_cabinet_id'] = $return_cabinet_id;
                $logInfo['operator'] = $currentUser->userCode??'无';
                $logInfo['operation_type'] = 1;
                $logInfo['operating_time'] = Warehouse::switchWareTime($time);
                $logInfo['content'] = '生成还柜单';
                $res2 = ReturnCabinetLog::insert($logInfo);
            }else{
                $res2 = false;
            }

            $file = [];
            foreach($path as $key => $value){
                $file[$key]['reservation_number_id'] = $reservation_number_id;
                $file[$key]['path'] = $value;
                $file[$key]['created_at'] = $time;
            }
            //插入预约单附件
            $res3 = ReservationManagementFile::insert($file);


            if($res1 && $res2 && $res3 ){
                return true;
            }else{
                return false;
            }
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * 更新预约单邮件信息
     * @author zt7242
     * @date 2019/4/29 17:04
     * @param $time
     * @param $ids
     * @return mixed
     */
    public static function updateEmailInfo($time,$ids)
    {

        return DB::update('UPDATE reservation_management SET email_reminder_number = email_reminder_number + 1, email_reminder_time = :email_reminder_time,
updated_at = :updated_at WHERE find_in_set(reservation_number_id,:ids)',['email_reminder_time'=>$time,'updated_at'=>$time,'ids'=>$ids]);

    }
    /**
     * 获取满足条件预约单邮箱
     * @author zt7242
     * @date 2019/4/29 16:11
     * @param $condition
     * @return mixed
     */
    public static function getReservationEmailByCondition($condition)
    {

//        DB::connection()->enableQueryLog();


        $cur_time = date('Y-m-d H:i:s');
        $que = new self;

        if (empty($condition['remaining_time']) && isset($condition['over_time'])){
            $over_time = date('Y-m-d H:i:s', strtotime('-'.$condition['over_time'].' hour'));
            $que->where('appointment_delivery_time','<=',$over_time);
        }
        //预约单状态为待审批或待送仓;
        $que = $que->where('status',StaticState::STATUS_WAIT_SEND_WAREHOUSE);
        if(isset($condition['frequency'])) {
            $que->where('email_reminder_number','<',$condition['frequency']);
        }

        if(isset($condition['interval_time'])) {
            $interval_time = date('Y-m-d H:i:s', strtotime('-'.$condition['interval_time'].' hour'));
            $que->where('email_reminder_time','<=',$interval_time);
        }
        //剩余时间小于配置时间
        //超时时间大于配置时间
        if(isset($condition['remaining_time']) && empty($condition['over_time'])) {
            $remaining_time = date('Y-m-d H:i:s', strtotime($condition['remaining_time'].' hour'));
            $que->where('appointment_delivery_time','>=',$cur_time);
            $que->where('appointment_delivery_time','<=',$remaining_time);
        }

         if (isset($condition['remaining_time']) && isset($condition['over_time'])){
            $over_time = date('Y-m-d H:i:s', strtotime('-'.$condition['over_time'].' hour'));
            $remaining_time = date('Y-m-d H:i:s', strtotime($condition['remaining_time'].' hour'));

             $que->where(function($query) use ($cur_time,$remaining_time,$over_time){
                     $query->whereBetween('appointment_delivery_time',[$cur_time,$remaining_time] )
                         ->orWhere('appointment_delivery_time', '<=', $over_time);
                 });
        }




        return $que->orderBy('created_at','desc')->pluck('email','reservation_number_id')->toArray();
//        dd(DB::getQueryLog());

    }

    /**
     * 根据预约单id查询预约单及入库单信息
     * @author zt7242
     * @date 2019/5/17 9:54
     * @param $id
     * @return ReservationManagement[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getResercationInfoById($id)
    {
        $query = self::with('InboundOrder')
            ->find($id);
        return $query;
    }
    /**
     * 根据情况导出预约单数据
     * @author zt7242
     * @date 2019/5/16 16:00
     * @param $condition
     * @return mixed
     */
    public static function exportReservationInfoByCondition($condition)
    {
        $query = new self();

        if(isset($condition['system'])){
            $query = $query->where('system',$condition['system']);
        }

        if(isset($condition['warehouse'])){
            $query = $query->where('warehouse_code',$condition['warehouse']);
        }

        if(isset($condition['cabinet_type'])){
            $query = $query->where('cabinet_type',$condition['cabinet_type']);
        }

        if(isset($condition['source'])){
            $query = $query->where('source',$condition['source']);
        }

        if(isset($condition['reservation_status'])){
            $query = $query->where('reservation_status',$condition['reservation_status']);
        }

        if(isset($condition['reservation_number'])){
            $query = $query->where('reservation_number',$condition['reservation_number']);
        }

        if(isset($condition['reservation_code'])){
            $query = $query->where('reservation_code',$condition['reservation_code']);
        }

        if(isset($condition['remaining_type']) && isset($condition['remaining_time'])){
            $ysTime = $condition['remaining_time'] * 24 * 3600 + time();
            $ysTime = date('Y-m-d H:i:s',$ysTime);
            switch ($condition['remaining_type']){
                case 1:
                    $query = $query->where('appointment_delivery_time','>', $ysTime);
                    break;
                case 2:
                    $query = $query->where('appointment_delivery_time','<', $ysTime);
                    break;
                case 3:
                    $star = date('Y-m-d 00:00:00',strtotime($ysTime));
                    $end = date('Y-m-d 23:59:59',strtotime($ysTime));
                    $query = $query->whereBetween('appointment_delivery_time',[$star,$end]);
                    break;
            }
        }

        if(isset($condition['time_type']) && isset($condition['time_day'])){
            $start_time = substr($condition['time_day'],0,19);
            $end_time = substr($condition['time_day'],22);

            switch($condition['time_type'] ){
                case 1:
                    $type = 'earliest_delivery_time';
                    break;
                case 2:
                    $type = 'latest_delivery_time';
                    break;
                case 3:
                    $type = 'appointment_delivery_time';
                    break;
                case 4:
                    $type = 'actual_arrival_time';
                    break;
                case 5:
                    $type = 'created_at';
                    break;
                default :
                    $type = 'updated_at';
            }

            if (!empty($start_time)) {
                $query = $query->where($type, '>', $start_time);
            }
            if (!empty($end_time)) {
                $query = $query->where($type, '<', $end_time);
            }
        }

        if(isset($condition['status'])){
            $query = $query->where('status',$condition['status']);
        }

        $query = $query->with('InboundOrder');

        $query = $query->whereHas('InboundOrder',function ($query) use ($condition){
            if (isset($condition['tracking_number'])) {  //跟踪号
                $query->where('tracking_number', $condition['tracking_number']);
            }

        });

        return  $query->orderBy('reservation_management.created_at', 'desc')->get();







    }


    /**
     * 创建预约单保存
     * @author zt7239
     * @param int $rm_id
     * @param array $rmData
     * @param array $requestData
     * @return bool
     */
    public static function saveData($rm_id,$rmData,$requestData,$inboundOrderData)
    {
        DB::beginTransaction();
        try {
                $nowTime = date('Y-m-d H:i:s');
                if(!$rm_id){//创建
                    $reservation_number = self::reservationNumberCreate();
                    $rmData['reservation_number'] = $reservation_number;
                    $rmData['created_at'] = $nowTime;
                    $reservation_number_id = DB::table('reservation_management')->insertGetId($rmData);
                    if($reservation_number_id){
                        foreach ($inboundOrderData as $k=>$v){
                            $inboundOrderData[$k]['reservation_number_id'] = $reservation_number_id;
                        }
                        $res1 = DB::table('inbound_order')->insert($inboundOrderData);
                        $rm_log[] = [
                            'reservation_number_id' => $reservation_number_id,
                            'operator_user_id' => CurrentUser::getCurrentUser()->userId,
                            'operator_user_name' => CurrentUser::getCurrentUser()->userCode,
                            'operator_type' => StaticState::OPERATOR_TYPE_ADD,
                            'operator_time' => Warehouse::switchWareTime($nowTime),
                            'content' => '新增',
                        ];
                        $res2 =DB::table('reservation_management_log')->insert($rm_log);

                        if($res1 && $res2){
                            DB::commit();
                            return $reservation_number;
                        }else{
                            DB::rollBack();
                            return false;
                        }

                    }
                }else{//编辑
                    $content = self::getInsertLogContent($rm_id,$requestData);
                    if(!$content){
                        DB::rollBack();
                        return false;
                    }
                    $rm_log[] = [
                        'reservation_number_id' => $rm_id,
                        'operator_user_id' => CurrentUser::getCurrentUser()->userId,
                        'operator_user_name' => CurrentUser::getCurrentUser()->userCode,
                        'operator_type' => StaticState::OPERATOR_TYPE_EDIT,
                        'operator_time' => Warehouse::switchWareTime($nowTime),
                        'content' => $content,
                    ];
                    $res1 = DB::table('reservation_management_log')->insert($rm_log);
                    $rmData['updated_at'] = $nowTime;
                    self::where('reservation_number_id',$rm_id)->update($rmData);
                    if($inboundOrderData){
                        DB::table('inbound_order')->where('reservation_number_id',$rm_id)->delete();
                        foreach ($inboundOrderData as $k=>$v){
                            $inboundOrderData[$k]['reservation_number_id'] = $rm_id;
                        }
                        $res2 = DB::table('inbound_order')->insert($inboundOrderData);
                    }
                    if($res1 && $res2){
                        DB::commit();
                        return true;
                    }else{
                        DB::rollBack();
                        return false;
                    }
                }
        } catch (\PDOException $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();

            return false;
        }
    }

    /**
     * 组装预约单日志的内容
     * @author zt7239
     * @param int $id
     * @param array $insertRmArr
     * @return string
     */
    public static function getInsertLogContent($id = 0,$insertRmArr = array())
    {
        $model = self::where('reservation_number_id',$id)->first();
        if(!$model){
            return false;
        }else{
            $model = $model->toArray();
        }
        $content = '';
        if(isset($insertRmArr['warehouse_code']) && $insertRmArr['warehouse_code'] != $model['warehouse_code']){
            $content .= '仓库'.$model['warehouse_code'].'修改为'.$insertRmArr['warehouse_code'].';';
        }
        if(isset($insertRmArr['type']) && $insertRmArr['type'] != $model['type']){
            $content .= '类型'.ReservationManagementService::getType($model['type']).'修改为'.ReservationManagementService::getType($insertRmArr['type']).';';
        }
        if(isset($insertRmArr['cabinet_type']) && $insertRmArr['cabinet_type'] != $model['cabinet_type']){
            $content .= '柜型'.ReservationManagementService::getCabinetType($model['cabinet_type']).'修改为'.ReservationManagementService::getCabinetType($insertRmArr['cabinet_type']).';';
        }
        if(isset($insertRmArr['container_type']) && $insertRmArr['container_type'] != $model['container_type']){
            $content .= '货柜类型'.ReservationManagementService::getContainerType($model['container_type']).'修改为'.ReservationManagementService::getContainerType($insertRmArr['container_type']).';';
        }
        if(isset($insertRmArr['file']) && $insertRmArr['file'] != $model['file']){
            $content .= '更新附件'.';';
        }
        if(isset($insertRmArr['customs_clearance_time']) && $insertRmArr['customs_clearance_time'] != $model['customs_clearance_time']){
            $content .= '清关时间'.$model['customs_clearance_time'].'修改为'.$insertRmArr['customs_clearance_time'].';';
        }
        if(isset($insertRmArr['arrival_time']) && $insertRmArr['arrival_time'] != $model['arrival_time']){
            $content .= '到港信息'.$model['arrival_time'].'修改为'.$insertRmArr['arrival_time'].';';
        }
        if(isset($insertRmArr['earliest_delivery_time']) && $insertRmArr['earliest_delivery_time'] != $model['earliest_delivery_time']){
            $content .= '最早提货时间'.$model['earliest_delivery_time'].'修改为'.$insertRmArr['earliest_delivery_time'].';';
        }
        if(isset($insertRmArr['latest_delivery_time']) && $insertRmArr['latest_delivery_time'] != $model['latest_delivery_time']){
            $content .= '最晚提货时间'.$model['latest_delivery_time'].'修改为'.$insertRmArr['latest_delivery_time'].';';
        }
        if(isset($insertRmArr['appointment_delivery_time']) && $insertRmArr['appointment_delivery_time'] != $model['appointment_delivery_time']){
            $content .= '预约递送时间'.$model['appointment_delivery_time'].'修改为'.$insertRmArr['appointment_delivery_time'].';';
        }
        if(isset($insertRmArr['contact_name']) && $insertRmArr['contact_name'] != $model['contact_name']){
            $content .= '联系人名称'.$model['contact_name'].'修改为'.$insertRmArr['contact_name'].';';
        }
        if(isset($insertRmArr['telephone']) && $insertRmArr['telephone'] != $model['telephone']){
            $content .= '电话'.$model['telephone'].'修改为'.$insertRmArr['telephone'].';';
        }
        if(isset($insertRmArr['email']) && $insertRmArr['email'] != $model['email']){
            $content .= '邮箱'.$model['email'].'修改为'.$insertRmArr['email'].';';
        }

        if($content){
            return $content;
        }else{
            return '编辑';
        }

    }

    /**
     * 获取预约单管理数据
     * @author zt7239
     * @param array $searchData
     * @param int $limit
     * @return array
     */
    public static function getDataByFilter($searchData,$limit)
    {
        $query = new self();

        $currentUser = CurrentUser::getCurrentUser();
        $wareTime = $currentUser->wareTimeNotUpdate;
        if($wareTime == '-12'){
            $wareCode = 'USEA';
        }else{
            $wareCode = 'USWE';
        }
        $query = $query->where('warehouse_code',$wareCode);

        if(isset($searchData['system'])){
            $query = $query->where('system',$searchData['system']);
        }

        if(isset($searchData['warehouse'])){
            $query = $query->where('warehouse_code',$searchData['warehouse']);
        }

        if(isset($searchData['cabinet_type'])){
            $query = $query->where('cabinet_type',$searchData['cabinet_type']);
        }

        if(isset($searchData['source'])){
            $query = $query->where('source',$searchData['source']);
        }

        if(isset($searchData['reservation_status'])){
            $query = $query->where('reservation_status',$searchData['reservation_status']);
        }

        if(isset($searchData['reservation_number'])){
            $query = $query->where('reservation_number',$searchData['reservation_number']);
        }

        if(isset($searchData['reservation_code'])){
            $query = $query->where('reservation_code',$searchData['reservation_code']);
        }

        if(isset($searchData['remaining_type']) && isset($searchData['remaining_time'])){
            $ysTime = $searchData['remaining_time'] * 24 * 3600 + time();
            $ysTime = date('Y-m-d H:i:s',$ysTime);
            switch ($searchData['remaining_type']){
                case 1:
                    $query = $query->where('appointment_delivery_time','>=', $ysTime);
                    break;
                case 2:
                    $query = $query->where('appointment_delivery_time','<=', $ysTime);
                    break;
                case 3:
                    $star = date('Y-m-d 00:00:00',strtotime($ysTime));
                    $end = date('Y-m-d 23:59:59',strtotime($ysTime));
                    $query = $query->whereBetween('appointment_delivery_time',[$star,$end]);
                    break;
            }
        }

        if(isset($searchData['time_type']) && isset($searchData['time_day'])){
            $start_time = substr($searchData['time_day'],0,19);
            $end_time = substr($searchData['time_day'],22);

            switch($searchData['time_type'] ){
                case 1:
                    $type = 'earliest_delivery_time';
                    break;
                case 2:
                    $type = 'latest_delivery_time';
                    break;
                case 3:
                    $type = 'appointment_delivery_time';
                    break;
                case 4:
                    $type = 'actual_arrival_time';
                    break;
                case 5:
                    $type = 'created_at';
                    break;
                default :
                    $type = 'updated_at';
            }

            if (!empty($start_time)) {
                $query = $query->where($type, '>=', Warehouse::opreationTimeZone($start_time));
            }
            if (!empty($end_time)) {
                $query = $query->where($type, '<=', Warehouse::opreationTimeZone($end_time));
            }

        }

        if(isset($searchData['status'])){
            $query = $query->where('status',$searchData['status']);
        }

        $query = $query->with('InboundOrder');

        $query = $query->whereHas('InboundOrder',function ($query) use ($searchData){
            if (isset($searchData['tracking_number'])) {  //跟踪号
                $query->where('tracking_number', $searchData['tracking_number']);
            }
            if (isset($searchData['sea_cabinet_number'])) {  //海柜号
                $query->where('sea_cabinet_number', $searchData['sea_cabinet_number']);
            }

        });

        $info = $query->orderBy('reservation_management.created_at', 'desc')->paginate($limit);

        $count = $info->total();

        return [
            'info' => $info->items(),
            'count' => $count
        ];


    }

    /**
     * 获取编辑页面的入库单数据
     * @author zt7239
     * @param $data
     * @return array
     */
    public function getEditInboundOrder($data)
    {
        $inbound = new InboundOrder();
        if(isset($data['id'])){
            $inbound = $inbound->where('reservation_number_id',$data['id']);
        }
        $inbound = $inbound->select('*');

        $inbound = $inbound->paginate($data['limit'],['*'],'page',isset($data['page']) ? $data['page']: 1);
        $count = $inbound->total();
        return [
            'info' => $inbound->items(),
            'count' => $count
        ];
    }

    /**
     * 废弃预约单
     * @author zt7239,zt12700
     * @param int $id
     * @return bool
     */
    public static function discard($id)
    {
        try {
            DB::transaction(function () use ($id) {

                self::where('reservation_number_id',$id)->update(['status'=>StaticState::STATUS_DISCARD]);

            });

            $currentUser = CurrentUser::getCurrentUser();
            $user_code = $currentUser->userCode;

            //拼接预约单编辑的日志
            $insertLog= [
                'reservation_number_id' => $id,
                'operator_user_name' => $user_code,
                'operator_type' => StaticState::OPERATOR_TYPE_EDIT,
                'operator_time' => Warehouse::opreationTimeZone(date('Y-m-d H:i:s')),
                'content' => '废弃预约单',
            ];

            ReservationManagementLog::insert($insertLog);

            DB::commit();

            return true;
        } catch (\PDOException $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();

            return false;
        }
    }

    /**
     * 审核预约单
     * @author zt7239
     * @param $requestData
     * @return bool
     */
    public static function updateReview($requestData)
    {
        $id = $requestData->id;

        DB::beginTransaction();
        try{
            $reservation_management = ReservationManagement::find($id);

            $reservation_management->appointment_delivery_time =  Warehouse::opreationTimeZone($requestData->appointment_delivery_time);
            $reservation_management->contact_name = $requestData->contact_name;
            $reservation_management->telephone =$requestData->telephone;
            $reservation_management->email = $requestData->email;
            $res = $reservation_management->save();
            if($res){
                $reservation_management->status=StaticState::STATUS_WAIT_SEND_WAREHOUSE;
                $reservation_management->reservation_status=StaticState::RESERVATION_STATUS_EFFECTIVE;
                $result = $reservation_management->save();
            }

            //判断是否登录
            $currentUser = CurrentUser::getCurrentUser();

            $reservation_management_log = new ReservationManagementLog();
            $reservation_management_log->reservation_number_id = $id;
            $reservation_management_log->operator_user_id = $currentUser->userId;
            $reservation_management_log->operator_user_name = $currentUser->userCode;
            $reservation_management_log->operator_type = 2;
            $reservation_management_log->operator_time = Warehouse::opreationTimeZone(date('Y-m-d H:i:s'));
            $reservation_management_log->content = __('auth.ReviewAppointmentForm');
            $reservation_management_log->save();



            DB::commit();
            return true;
        }catch (\PDOException $exception){
            Log::info($exception->getMessage());
            DB::rollBack();
            return false;
        }

    }

    /**
     * 预约审核--更新状态,生成预约码
     * @author zt7239
     * @param int $id
     * @return bool
     */
    public static function appointmentReview($id = 0)
    {
        try {
            DB::transaction(function () use ($id) {

               $res = self::where('reservation_number_id',$id)->update(['status'=>StaticState::STATUS_WAIT_RESERVATION]);

               if($res){
                   $reservationCode = ReservationManagement::reservationCodeCreate();
                   self::where('reservation_number_id',$id)->update(['reservation_code'=>$reservationCode]);
               }else{
                   DB::rollBack();

                   return false;
               }
            });
            DB::commit();

            return true;
        } catch (\PDOException $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();

            return false;
        }

    }

    /**
     * 预约递送时间如果超时，更新预约状态
     * @author zt7239
     * @param $id
     * @return bool
     */
    public static function updateReservationStatus($id)
    {
        try {
            DB::transaction(function () use ($id) {

                $res = self::where('reservation_number_id',$id)->update(['reservation_status'=>StaticState::RESERVATION_STATUS_EXPIRED]);

                if(!$res){
                    DB::rollBack();

                    return false;
                }
            });
            DB::commit();

            return true;
        } catch (\PDOException $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();

            return false;
        }
    }

    /**
     * 获取详情
     * @author zt7239
     * @param $id
     * @return mixed
     */
    public static function getDetail($id)
    {
        $collect = self::where('reservation_number_id',$id);
        $collect = $collect->with('InboundOrder')->first()->toArray();

        return $collect;
    }

    /**
     * 获取预约单的附件图片
     * @author zt7239
     * @param $id
     * @return mixed
     */
    public static function getFile($id)
    {
        $res = ReservationManagementFile::where('reservation_number_id',$id)->get()->toArray();
        return $res;
    }

    /**
     * 获取预约单日志
     * @author zt7239
     * @param $id
     * @return mixed
     */
    public static function getLog($id)
    {
        return ReservationManagementLog::where('reservation_number_id',$id)->get()->toArray();
    }

    /**
     * 编辑OMS过来的预约单
     * @author zt7239
     * @param string $reservationNumber
     * @param array $insertRmArr
     * @param array $inbound_order_info
     * @return bool
     */
    public static function updateOMSReservationNumberData($reservationNumber = '',$insertRmArr = array(),$inbound_order_info=array()){
        $model = self::where('reservation_number',$reservationNumber)->first();
        if(!$model){
            return [
                'code' => 404,
                'msg' => __('auth.OrderInformationNotObtained')
            ];
        }else{
            $model = $model->toArray();
        }

        $content = '';
        if(isset($insertRmArr['warehouse_code']) && $insertRmArr['warehouse_code'] != $model['warehouse_code']){
            $content .= '仓库'.$model['warehouse_code'].'修改为'.$insertRmArr['warehouse_code'].';';
        }
        if(isset($insertRmArr['type']) && $insertRmArr['type'] != $model['type']){
            $content .= '类型'.ReservationManagementService::getType($model['type']).'修改为'.ReservationManagementService::getType($insertRmArr['type']).';';
        }
        if(isset($insertRmArr['cabinet_type']) && $insertRmArr['cabinet_type'] != $model['cabinet_type']){
            $content .= '柜型'.ReservationManagementService::getCabinetType($model['cabinet_type']).'修改为'.ReservationManagementService::getCabinetType($insertRmArr['cabinet_type']).';';
        }
        if(isset($insertRmArr['container_type']) && $insertRmArr['container_type'] != $model['container_type']){
            $content .= '货柜类型'.ReservationManagementService::getContainerType($model['container_type']).'修改为'.ReservationManagementService::getContainerType($insertRmArr['container_type']).';';
        }
        if(isset($insertRmArr['file']) && $insertRmArr['file'] != $model['file']){
            $content .= '更新附件'.';';
        }
        if(isset($insertRmArr['customs_clearance_time']) && $insertRmArr['customs_clearance_time'] != $model['customs_clearance_time']){
            $content .= '清关时间'.$model['customs_clearance_time'].'修改为'.$insertRmArr['customs_clearance_time'].';';
        }
        if(isset($insertRmArr['arrival_time']) && $insertRmArr['arrival_time'] != $model['arrival_time']){
            $content .= '到港信息'.$model['arrival_time'].'修改为'.$insertRmArr['arrival_time'].';';
        }
        if(isset($insertRmArr['earliest_delivery_time']) && $insertRmArr['earliest_delivery_time'] != $model['earliest_delivery_time']){
            $content .= '最早提货时间'.$model['earliest_delivery_time'].'修改为'.$insertRmArr['earliest_delivery_time'].';';
        }
        if(isset($insertRmArr['latest_delivery_time']) && $insertRmArr['latest_delivery_time'] != $model['latest_delivery_time']){
            $content .= '最晚提货时间'.$model['latest_delivery_time'].'修改为'.$insertRmArr['latest_delivery_time'].';';
        }
        if(isset($insertRmArr['appointment_delivery_time']) && $insertRmArr['appointment_delivery_time'] != $model['appointment_delivery_time']){
            $content .= '预约递送时间'.$model['appointment_delivery_time'].'修改为'.$insertRmArr['appointment_delivery_time'].';';
        }
        if(isset($insertRmArr['contact_name']) && $insertRmArr['contact_name'] != $model['contact_name']){
            $content .= '联系人名称'.$model['contact_name'].'修改为'.$insertRmArr['contact_name'].';';
        }
        if(isset($insertRmArr['telephone']) && $insertRmArr['telephone'] != $model['telephone']){
            $content .= '电话'.$model['telephone'].'修改为'.$insertRmArr['telephone'].';';
        }
        if(isset($insertRmArr['email']) && $insertRmArr['email'] != $model['email']){
            $content .= '邮箱'.$model['email'].'修改为'.$insertRmArr['email'].';';
        }

        //拼接入库单的数据
        $insertInboundOrder = array();
        foreach($inbound_order_info as $k =>$v){
            $insertInboundOrder[] = [
                'reservation_number_id' => $model['reservation_number_id'],
                'inbound_order_number' => $v['inbound_order_number'],
                'customer_code' => $v['customer_code'],
                'tracking_number' => isset($v['tracking_number'])?$v['tracking_number']:'',
                'warehouse_code' => $v['warehouse_code'],
                'warehouse_name' => $v['warehouse_name'],
                'products_number' => $v['products_number'],
                'sea_cabinet_number' => isset($v['sea_cabinet_number']) ? $v['sea_cabinet_number'] : '',
                'sku_species_number' =>  $v['sku_species_number'],
                'box_number' => $v['box_number'],
                'weight' => $v['weight'],
                'volume' => $v['volume'],
                'created_at' => $v['created_at'],
            ];
        }

        //拼接预约单编辑的日志
        $insertLog[] = [
            'reservation_number_id' => $model['reservation_number_id'],
            'operator_user_name' => $insertRmArr['operator'],
            'operator_type' => StaticState::OPERATOR_TYPE_EDIT,
            'operator_time' => $insertRmArr['operating_time'],
            'content' => $content,
        ];

        DB::beginTransaction();
        try{
            $insertRmArr['updated_at'] = date('Y-m-d H:i:s');
            $res = self::where('reservation_number',$reservationNumber)->update($insertRmArr);

            if($res){
                DB::table('inbound_order')->where('reservation_number_id',$model['reservation_number_id'])->delete();

                $res1 = DB::table('inbound_order')->insert($insertInboundOrder);

                $res2 = DB::table('reservation_management_log')->insert($insertLog);

                if($res1 && $res2){
                    DB::commit();
                    return true;
                }else{
                    DB::rollBack();
                    return false;
                }
            }else{
                DB::rollBack();
                return false;
            }

        }catch (\Exception $e){
            Log::info('编辑OMS预约单号'.$reservationNumber.'失败:'.$e->getMessage());
            DB::rollBack();
            return false;
        }
    }

    /**
     * 更新谷仓接口过来的状态
     * @author zt7239
     * @param string $reservation_number
     * @param string $status
     * @param string $operator
     * @param string $operating_time
     * @return mixed
     */
    public static function updateReservationNumberStatus($reservation_number,$status,$operator,$operating_time)
    {
        $rmData = self::where('reservation_number',$reservation_number)->first();
        if($rmData){
            $rmData = $rmData->toArray();
        }else{
            return [
                'code' =>1,
                'msg' => __("auth.NoSingleNumber")
            ];
        }

        DB::beginTransaction();
        if($status == 1){
            $res = self::where('reservation_number',$reservation_number)->update(['status'=>StaticState::STATUS_WAIT_RESERVATION, 'operator'=>$operator, 'operating_time'=>$operating_time, 'updated_at'=>date('Y-m-d H:i:s'),]);

            $reservation_code = self::reservationCodeCreate();
            $res1 = self::where('reservation_number',$reservation_number)->update(['reservation_code'=>$reservation_code]);

            $log[] = [
                'reservation_number_id' =>$rmData['reservation_number_id'],
                'operator_user_name' => $operator,
                'operator_time' => $operating_time,
                'operator_type' => 2,
                'content' => '状态'.ReservationManagementService::getStatus($rmData['status']).'更新为'.ReservationManagementService::getStatus(StaticState::STATUS_WAIT_RESERVATION),
            ];
            $res2 = DB::table('reservation_management_log')->insert($log);

            if($res && $res1 && $res2){
                DB::commit();
                return true;
            }else{
                DB::rollback();
                return false;
            }
        }else{
            $res = self::where('reservation_number',$reservation_number)->update(['status'=>StaticState::STATUS_DISCARD, 'reservation_status'=>StaticState::RESERVATION_STATUS_NOT_EFFECTIVE, 'operator'=>$operator, 'operating_time'=>$operating_time, 'updated_at'=>date('Y-m-d H:i:s'),]);

            $log[] = [
                'reservation_number_id' =>$rmData['reservation_number_id'],
                'operator_user_name' => $operator,
                'operator_time' => $operating_time,
                'operator_type' => 2,
                'content' => '状态'.ReservationManagementService::getStatus($rmData['status']).'更新为'.ReservationManagementService::getStatus(StaticState::STATUS_DISCARD),
            ];
            $res1 = DB::table('reservation_management_log')->insert($log);

            if($res && $res1 ){
                DB::commit();
                return true;
            }else{
                DB::rollback();
                return false;
            }
        }

    }


    /**
     * 谷仓查询接口获取数据
     * @author zt7239
     * @param array $data
     * @return ReservationManagement
     */
    public function getOMSReservationOrder($data = array()){

        $collect = self::query() ;

        if(isset($data['reservation_number'])){
            $collect = $collect->where('reservation_management.reservation_number',$data['reservation_number']);
        }

        if(isset($data['warehouse_code'])){
            $collect = $collect->where('reservation_management.warehouse_code',$data['warehouse_code']);
        }

        if(isset($data['customer_code'])){
            $collect = $collect->where('reservation_management.customer_code',$data['customer_code']);
        }

        if(isset($data['status'])){
            $collect = $collect->where('reservation_management.status',$data['status']);
        }
        if(isset($data['type'])){
            if($data['type'] == 1){
                $collect = $collect->where('reservation_management.reservation_number',$data['order_number']);
            }elseif ($data['type'] == 2){
                $collect = $collect->where('reservation_management.reservation_code',$data['order_number']);
            }
        }

        if(isset($data['created_start_time'])){
            $collect->where('reservation_management.created_at','>=',$data['created_start_time']);
        }

        if(isset($data['created_end_time'])){
            $collect->where('reservation_management.created_at','<=',$data['created_end_time']);
        }

        $collect = $collect->where('source',StaticState::SOURCE_CLIENT);//谷仓那边只查询来源为客户的数据
        $collect = $collect->select('*');

        $collect = $collect->with('InboundOrder') ;

//        $collect = $collect->with(['InboundOrder'=>function($query){
//            return $query->select('reservation_number_id','inbound_order_number', 'tracking_number','sea_cabinet_number');
//        }]);

        $collect = $collect->whereHas('InboundOrder' ,function($query) use($data){
            if(isset($data['type'])){
                if($data['type'] == 3){
                    return   $query->where('inbound_order_number',$data['order_number']);
                }elseif($data['type'] == 4){
                    return  $query->where('sea_cabinet_number',$data['order_number']);
                }else{
                    return  $query ;
                }

            }
        }) ;
        $collect = $collect->orderBy('reservation_management.reservation_number_id','desc');

        if(isset($data['page']) ){
            $collect = $collect->paginate($data['pagesize'],['*'],'page',$data['page'])->toArray();
        }else{
            $collect = $collect->paginate(20,['*'],'page',1)->toArray();
        }

        return $collect;
    }


    /**
     * OMS接口获取详情
     * @author zt7239
     * @param array $data
     * @return ReservationManagement|array|bool
     */
    public function getOMSReservationOrderDetail($data = array())
    {
        $collect = $this ;

        if(isset($data['reservation_number'])){
            $collect = $collect->where('reservation_management.reservation_number',$data['reservation_number']);
        }

        $collect = $collect->select('*');

        if(!$collect){
            return false;
        }

        $collect = $collect->with('InboundOrder');
        $collect = $collect->first()->toArray();

        $log = ReservationManagementLog::where('reservation_number_id',$collect['reservation_number_id'])->select('operator_user_name','operator_type','operator_time','content')->get()->toArray();

        $collect['reservation_management_log'] = $log;

        return $collect;
    }

    /**
     * 获取谷仓那边过来的预约单详情是否属于该客户的
     * @author zt7239
     * @param $reservation_number
     * @param $customer_code
     * @return \Illuminate\Database\Eloquent\Builder|Model|null|object|static
     */
    public function getCustomer($reservation_number,$customer_code)
    {
        $collect = self::query();

        $collect = $collect->where(['reservation_number'=>$reservation_number,'customer_code'=>$customer_code])->first();

        return $collect;

    }

    /**
     * 校验入库单是否已创建
     * @author zt7239
     * @param array $inbound_order
     * @param int $rm_id
     * @return array
     */
    public static function validaterData($inbound_order = array(),$rm_id = 0,$warehouse_code ='')
    {
        $rm = new OWMSService();
        if(!$rm_id){//创建
            foreach ($inbound_order as $v){
                $res = $rm->getApiInboundOrder($v['inbound_order_number'],$v['tracking_number'],$v['warehouse_code'],$v['sea_cabinet_number']);//调接口获取
                if(!is_array($res)){
                    return [
                        'code' => 1,
                        'msg' => __('auth.SingleNumber').':'.$v['inbound_order_number'].' '.$res,
                    ];
                }

                $res1 = InboundOrder::filterInboundData($v['inbound_order_number']);
                if($res1){
                    return [
                        'code' => 1,
                        'msg' => __('auth.SingleNumber').':'.$v['inbound_order_number'].' '.__("auth.AppointmentOrderHasBeenCreated")
                    ];
                }
            }
        }else{//编辑
            $collect = new self();
            $collect = $collect->where('reservation_number_id',$rm_id);
            $collect = $collect->select('*');
            $collect = $collect->with('InboundOrder')->first() ;

            if($collect){
                $collect = $collect->toArray();
                $inboundOrderData = array_merge($inbound_order,$collect['inbound_order']);
                $inboundOrderData = array_merge($collect['inbound_order'], $inboundOrderData);

                $arr = array_count_values(array_column($inboundOrderData, 'inbound_order_number'));
                $arr =  array_filter($arr, function ($value) {
                    if ($value < 2) {
                        return $value;
                    }
                });

                foreach ($arr as $k=>$v){
                    $res = $rm->getApiInboundOrder($k,'',$warehouse_code,'');//调接口获取
                    if(!is_array($res)){
                        return [
                            'code' => 1,
                            'msg' => __('auth.SingleNumber').':'.$v['inbound_order_number'].' '.$res,
                        ];
                    }

                    $res1 = InboundOrder::filterInboundData($v['inbound_order_number']);
                    if($res1){
                        return [
                            'code' => 1,
                            'msg' => __('auth.SingleNumber').':'.$k.' '.__("auth.AppointmentOrderHasBeenCreated")
                        ];
                    }
                }
            }else{
                return [
                    'code' => 1,
                    'msg' => __("auth.OrderInformationNotObtained")
                ];
            }


        }

    }


    /**
     * 导出
     * @author zt7239
     * @param $params
     * @return bool
     */
    public static function export($params)
    {
        $time = Warehouse::opreationTimeZone(date('Y-m-d H:i:s'));
        DB::beginTransaction();
        $info = Download::insertGetId([
            'download_name'=>$params['exports_name'],
            'menu_id'=>1,
            'created_at'=>$time,
            'updated_at'=>$time,
        ]);
        if($info){
            $res = DownloadTimer::insert([
                'download_id'=>$info,
                'status'=>0,
                'menu_id'=>1,
                'download_condition'=>serialize($params['data']),
                'created_at'=>$time
            ]);
        }

        if($res && $info){
            DB::commit();
            return true;
        }else{
            DB::rollback();
            return false;
        }
    }


    /**
     * 生成预约单号
     * @author zt7239
     * @return string
     */
    public static function reservationNumberCreate()
    {
        $db = new self();
        $no = $db->whereDate('created_at', date('Y-m-d'))->select(DB::raw('right(100000+count(*)+1,5) as NO'))->first();  //后5位流水号

        return 'YY'.date('Ymd').$no->NO;
    }

    /**
     * 生成唯一8位预约码
     * @author zt7239
     * @return string
     */
    public static function reservationCodeCreate() {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        return  $d;
    }



}
