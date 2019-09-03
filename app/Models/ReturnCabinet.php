<?php

namespace App\Models;

use App\Auth\Common\AjaxResponse;
use App\Auth\Common\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MailController;
use Dompdf\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Auth\Common\CurrentUser;

/**
 * 路由模型
 */
class ReturnCabinet extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'return_cabinet';

    /**
     * 与模型关联的数据表主键
     * 还柜单号id
     * @var string
     */
    protected $primaryKey = 'return_cabinet_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     ** 还柜单跟预约单表关联关系
     * @author zt12700
     */
    public function rem()
    {
        //关联的模型类名, 关系字段
        return $this->hasOne('App\Models\ReservationManagement','reservation_number_id','reservation_number_id');
    }


    /**
     ** 还柜单跟入库单表关联关系
     * 一个预约单号对应多个入库单
     * @author zt12700
     */
    public function inbound()
    {
        //关联的模型类名, 关系字段
        return $this->hasMany('App\Models\InboundOrder','reservation_number_id','reservation_number_id');
    }

    /**
     ** 还柜单跟还柜单附件表关联关系
     * 一个还柜单号对应多个附件
     * @author zt12700
     */
    public function file()
    {
        //关联的模型类名, 关系字段
        return $this->hasMany('App\Models\ReturnCabinetFile','return_cabinet_id');
    }

    /**
     ** 还柜单跟还柜单日志表关联关系
     * 一个还柜单号对应多个日志
     * @author zt12700
     */
    public function log()
    {
        //关联的模型类名, 关系字段
        return $this->hasMany('App\Models\ReturnCabinetLog','return_cabinet_id');
    }

    /**
     * 通过预约单号获取还柜信息
     * @author zt7242
     * @date 2019/5/7 18:13
     * @param $reservation_id
     * @return mixed
     */
    public static function getCabinetInfoByReservationId($reservation_id)
    {
        return self::where('reservation_number_id',$reservation_id)->get()->first();
    }

    /**
     * 更新还柜单信息
     * @author zt7242
     * @date 2019/5/8 14:38
     * @param $cabinet_id
     * @param $actual_start_time
     * @param $actual_end_time
     * @return mixed
     */
    public static function updateReturnCabinetInfo($cabinet_id, $actual_start_time, $actual_end_time)
    {
        $currentUser = CurrentUser::getCurrentUser();

        try{
            $cabinet = ReturnCabinet::find($cabinet_id);
            if(empty($cabinet)){
                return false;
            }elseif ($cabinet->status == StaticState::RETURN_STATUS_ALREADY ){
                    //还柜状态为已卸柜
                return false;
            }

            $time = date('Y-m-d H:i:s');
            //更新还柜单信息
            $res1 = self::where('return_cabinet_id',$cabinet_id)->update([
                'actual_start_time' => Warehouse::switchWareTime($actual_start_time),
                'actual_end_time' => Warehouse::switchWareTime($actual_end_time),
                'updated_at' => $time,
                'status' => StaticState::RETURN_STATUS_ALREADY   //已卸柜
            ]);

            //插入还柜日志
            $logInfo['return_cabinet_id'] = $cabinet_id;
            $logInfo['operator'] = $currentUser->userCode??'无';
            $logInfo['operation_type'] = 2;
            $logInfo['operating_time'] = Warehouse::switchWareTime($time);
            $logInfo['content'] = '待卸柜变为已卸柜';
            $res2 = ReturnCabinetLog::insert($logInfo);

            if($res1 && $res2 ){
                return true;
            }else{
                return false;
            }

        }catch (\Exception $e){
            return false;
        }


    }

    /**
     * 更新还柜信息及插入还柜附件
     * @author zt7242
     * @date 2019/5/14 18:49
     * @param $path
     * @param $cabinet_id
     * @param $actual_return_time
     * @return bool
     */
    public static function updateAndInsertCabinetInfo($path,$cabinet_id,$actual_return_time)
    {
        $currentUser = CurrentUser::getCurrentUser();
        $time = date('Y-m-d H:i:s');
        //开启事务
        try{
            $cabinet = ReturnCabinet::find($cabinet_id);
            if(empty($cabinet)){
                return false;
            }elseif ($cabinet->status == StaticState::RETURN_STATUS_RETURN_END ){
                //还柜状态为已还柜
                return false;
            }

            //更新预约单状态变为已送仓，预约状态变为已完结
            $res1 = self::where('return_cabinet_id',$cabinet_id)->update(['status'=>StaticState::RETURN_STATUS_RETURN_END,
                'actual_return_time'=>Warehouse::switchWareTime($actual_return_time),'updated_at' => Warehouse::switchWareTime($time)]);

            $file = [];
            foreach($path as $key => $value){
                $file[$key]['return_cabinet_id'] = $cabinet_id;
                $file[$key]['path'] = $value;
                $file[$key]['created_at'] = $time;
            }

            //插入还柜附件
            $res2 = ReturnCabinetFile::insert($file);

            //插入还柜日志
            $logInfo['return_cabinet_id'] = $cabinet_id;
            $logInfo['operator'] = $currentUser->userCode??'无';
            $logInfo['operation_type'] = 2;
            $logInfo['operating_time'] = Warehouse::switchWareTime($time);
            $logInfo['content'] = '待还柜变为已还柜';
            $res3 = ReturnCabinetLog::insert($logInfo);


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
     * 导出还柜单信息
     * @author zt7242
     * @date 2019/4/25 13:07
     * @param $condition
     * @return array
     */
    public static function exportReturnCabinetInfoByCondition($condition)
    {
        $query = self::query();
        if(isset($condition['system'])) {  //系统
            $query->where('system',$condition['system']);
        }
        if(isset($condition['warehouse'])) { //仓库
            $query->where('warehouse_code',$condition['warehouse']);
        }

        if(isset($condition['cabinet_type'])) { //柜型
            $query->where('cabinet_type',$condition['cabinet_type']);
        }

        if(isset($condition['source'])) { //来源
            $query->where('source',$condition['source']);
        }

        if(isset($condition['time_type']) && isset($condition['time_during'])){
            $start_time = substr($condition['time_during'],0,19);
            $end_time = substr($condition['time_during'],22);

            switch($condition['time_type'] ){
                case 1:
                    $type = 'actual_start_time';
                    break;
                case 2:
                    $type = 'actual_end_time';
                    break;
                case 3:
                    $type = 'actual_return_time';
                    break;
                case 4:
                    $type = 'notice_return_time';
                    break;
                default :
                    $type = 'operating_time';
            }

            if (!empty($start_time)) {
                $query->where($type, '>=', $start_time);
            }
            if (!empty($end_time)) {
                $query->where($type, '<=', $end_time);
            }
        }

        if(isset($condition['status'])) { //状态
            $query->where('status',$condition['status']);
        }

        $info = $query->with('rem')->with('inbound');

        $info = $info->whereHas('rem',function ($query) use ($condition){
            if (isset($condition['reservation_number'])) {  //预约单号
                $query->where('reservation_number', $condition['reservation_number']);
            }

            if (isset($condition['reservation_code'])) {  //预约码
                $query->where('reservation_code', $condition['reservation_code']);
            }
        });

        $info = $info->whereHas('inbound',function ($query) use ($condition){
            if (isset($condition['tracking_number'])) {  //跟踪号
                $query->where('tracking_number', $condition['tracking_number']);
            }

        });
        return  $info->orderBy('return_cabinet.created_at', 'desc')->get();
    }
    /**
     * 获取运单列表
     * @author zt12700
     * @date 2019/4/23 14:51
     * @param $data
     * @param $limit
     * @return array
     */
    public static function getList($data,$limit)
    {
        $query = self::query();

        $currentUser = CurrentUser::getCurrentUser();
        $wareTime = $currentUser->wareTimeNotUpdate;
        if($wareTime == '-12'){
            $wareCode = 'USEA';
        }else{
            $wareCode = 'USWE';
        }
        $query = $query->where('warehouse_code',$wareCode);

        if(isset($data['system'])) {  //系统
            $query->where('system',$data['system']);
        }
        if(isset($data['warehouse'])) { //仓库
            $query->where('warehouse_code',$data['warehouse']);
        }

        if(isset($data['cabinet_type'])) { //柜型
            $query->where('cabinet_type',$data['cabinet_type']);
        }

        if(isset($data['source'])) { //来源
            $query->where('source',$data['source']);
        }

        if(isset($data['time_type']) && isset($data['time_during'])){
            $start_time = substr($data['time_during'],0,19);
            $end_time = substr($data['time_during'],22);

            switch($data['time_type'] ){
                case 1:
                    $type = 'actual_start_time';
                    break;
                case 2:
                    $type = 'actual_end_time';
                    break;
                case 3:
                    $type = 'actual_return_time';
                    break;
                case 4:
                    $type = 'notice_return_time';
                    break;
                default :
                    $type = 'operating_time';
            }

            if (!empty($start_time)) {
                $query->where($type, '>=', Warehouse::opreationTimeZone($start_time));
            }

            if (!empty($end_time)) {
                $query->where($type, '<=', Warehouse::opreationTimeZone($end_time));
            }
        }

        if(isset($data['status'])) { //状态
            $query->where('status',$data['status']);
        }

        $info = $query->with('rem')->with('inbound');

        $info = $info->whereHas('rem',function ($query) use ($data){
            if (isset($data['reservation_number'])) {  //预约单号
                $query->where('reservation_number', $data['reservation_number']);
            }

            if (isset($data['reservation_code'])) {  //预约码
                $query->where('reservation_code', $data['reservation_code']);
            }
        });

        $info = $info->whereHas('inbound',function ($query) use ($data){
            if (isset($data['tracking_number'])) {  //跟踪号
                $query->where('tracking_number', $data['tracking_number']);
            }
            if (isset($data['sea_cabinet_number'])) {  //海柜号
                $query->where('sea_cabinet_number', $data['sea_cabinet_number']);
            }

        });
        $info = $info->orderBy('return_cabinet.created_at', 'desc')->paginate($limit);

        $count = $info->total();

        return [
            'info' => $info->items(),
            'count' => $count
        ];

    }

    /**
     * 查询还柜id对应的仓库
     * @author zt7239
     * @param $cabinet_id
     * @return bool
     */
    public static function warehouseExistByCabinetId($cabinet_id)
    {
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime = $currentUser->wareTime;
        if($wareTime == '-12'){
            $wareCode = 'USEA';
        }else{
            $wareCode = 'USWE';
        }

        $res = self::where('warehouse_code',$wareCode)->where('return_cabinet_id',$cabinet_id)->first();

        if($res){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 查看功能
     * @author zt12700
     * @param $id
     * @return mixed
     */

    public static function edit($id){
        $res = ReturnCabinet::find($id)->rem()->get();

        return $res;
    }

    /**
     * 邮件通知
     * @author zt12700
     */
    public static function emil($params)
    {
        //根据id查询状态
        $id = $params['id'];
        $info = ReturnCabinet::find($id);
        $status = $info['status'];
        try{
            $content = $params['reservation_number']."  is empty and ready for pickup, please pickup empty asap.";
            $to = $params['email'];
            $subject = "return cabinet";
            $mail = new MailController();
            $mail->send($content,$to,$subject);


            //$res = true;
            DB::beginTransaction();
            $currentUser = CurrentUser::getCurrentUser();
            if($status==2){
                //改变数据库状态
                $info->status = 3;
                $info->notice_return_time = Warehouse::switchWareTime($params['notice_return_time']);
                $info->save();

                //将操作保存到日志表中
                $logres = ReturnCabinetLog::insert([
                    'return_cabinet_id'=>$id,
                    'operator'=>$currentUser->userCode,
                    'operation_type'=>2,
                    'operating_time'=>Warehouse::switchWareTime(date('Y-m-d H:i:s',time())),
                    'content'=>'已卸柜变为待还柜'
                ]);


            }else{
                $info->notice_return_time = Warehouse::switchWareTime($params['notice_return_time']);
                $info->save();

                //将操作保存到日志表中
                $logres = ReturnCabinetLog::insert([
                    'return_cabinet_id'=>$id,
                    'operator'=>$currentUser->userCode,
                    'operation_type'=>2,
                    'operating_time'=>Warehouse::switchWareTime(date('Y-m-d H:i:s',time())),
                    'content'=>'待还柜邮件发送',
                ]);

            }

            if($logres){
                DB::commit();
                return true;
            }else{
                DB::rollback();
                Log::error($logres);
                return false;
            }

        }catch(\Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * d导出
     * @author zt12700
     * @param $params
     * @return \Illuminate\Http\JsonResponse
     */
    public static function down($params)
    {
        DB::beginTransaction();
        $info = Download::insertGetId([
                    'download_name'=>$params['exports_name'],
                    'menu_id'=>2,
                    'created_at'=>date('Y-m-d H:i:s',time()),
                    'updated_at'=>date('Y-m-d H:i:s',time()),
                ]);
        if($info){
            $res = DownloadTimer::insert([
                'download_id'=>$info,
                'status'=>0,
                'menu_id'=>2,
                'download_condition'=>serialize($params['data']),
                'created_at'=>date('Y-m-d H:i:s',time())
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

}
