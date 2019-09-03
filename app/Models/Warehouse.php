<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use app\Auth\Common\CurrentUser;

class Warehouse extends Model
{

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'warehouse';
    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'warehouse_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 不能被批量赋值的属性
     *
     * @var array
     */
//    protected $guarded = [];

    /**
     * 获取所有仓库信息
     * @author zt6535
     * CreateTime: 2019/3/12 13:34
     * @return Role[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getWarehouses()
    {
        return self::all();
    }

    public static function getTimeZoneByCode($warehouse_code)
    {
        return self::where('warehouse_code',$warehouse_code)->select('time_zone')->first();
    }
    /**
     * 通过id获取仓库信息
     * @author zt7239
     * @param $warehouse_id
     * @return mixed
     */
    public static function getWarehousesById($warehouse_id)
    {
        return self::where('warehouse_id',$warehouse_id)->first()->toArray();
    }

    /**
     * @description定时写入仓库数据
     * @author zt7242
     * @date 2019/4/10 19:39
     * @param $warehouse
     */
    public static function writeWarehousesTimer($warehouse)
    {
        DB::beginTransaction();

        self::deleteWarehouses();
        $count = self::countWarehouses();
        if($count == 0){
            $time = date('Y-m-d H:i:s');
            $newWare =[];
            foreach($warehouse as $k =>$v){
                $newWare[$k]['warehouse_id'] = $v['Warehouse_Id'];
                $newWare[$k]['wms_ware_code'] = $v['Warehouse_Code'];
                $newWare[$k]['warehouse_name'] = $v['Warehouse_Name'];
                $newWare[$k]['warehouse_en'] = $v['warehouse_en'];
                $newWare[$k]['warehouse_status'] = $v['Warehouse_Status'];
                $newWare[$k]['created_at'] = $time;
                $newWare[$k]['updated_at'] = $time;
            }
            $res = self::insertWarehouses($newWare);

            if($res){
                DB::commit();
                Log::info('仓库数据：保存成功!');

            }else{
                DB::rollback();
                Log::info('仓库数据：保存失败!');
            }
        }else{
            DB::rollback();
            Log::info('仓库数据：保存失败!');

        }

    }


    /**
     * 清空仓库数据
     * @author zt7242
     */
    public static function deleteWarehouses()
    {
        return self::query()->delete();
    }

    /**
     * 统计仓库数据
     * @author zt7242
     */
    public static function countWarehouses()
    {
        return self::count();
    }

    /**
     * @description批量插入仓库数据
     * @author zt7242
     * @date 2019/4/4 15:13
     * @param $data
     * @return mixed
     */
    public static function insertWarehouses($data)
    {
        if(empty($data)) return false;
        return self::insert($data);
    }

    /**
     * 根据条件仓库信息
     * @author zt6768
     * @param array $conditon 条件
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getInfoByConditon($conditon)
    {
        $where = [];

        if (isset($conditon['warehouse_code'])) {
            $where[] = ['wms_ware_code', '=', $conditon['warehouse_code']];
        }

        return static::where($where)->first();
    }

    /**
     * 更改仓库时间
     * CN 0(中国)  USEA -12（美东） USWE -15 (美西)
     * @author zt12700
     */
    public static function switchTimeZone($time)
    {
        if(empty($time)){
            return '';
        }
        if(isset($action) && !empty($action) ){
            return '';
        }

        //从session获取数据
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime =  $currentUser->wareTime;

        if ($wareTime== '-12'){
            $timeZone = -12;
        }elseif($wareTime== '-15'){
            $timeZone = -15;
        }else{
            $timeZone = 0;
        }

        $info = date('Y-m-d H:i:s',strtotime($time)+$timeZone*3600);
        return $info;
    }


    /**
     * 根据第一次登录选择仓库更改时间
     *  USEA -12（美东） USWE -15 (美西)
     * @author zt12700
     */
    public static function switchWareTime($time)
    {
        if(empty($time)){
            return '';
        }
        if(isset($action) && !empty($action) ){
            return '';
        }

        //从session获取数据,登录仓库，当前选择仓库，存入北京时间
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime =  $currentUser->wareTime;

        if ($wareTime== '-12'){
            $timeZone = 12;
        }elseif($wareTime== '-15'){
            $timeZone = 15;
        }else{
            $timeZone = 0;
        }

        $info = date('Y-m-d H:i:s',strtotime($time)+$timeZone*3600);
        return $info;
    }


    /**
     * 根据时区更改时间
     * @@param $timeZone 时区
     * @author zt3361
     */
    public static function switchTimeByZone($time)
    {
        if(empty($time)){
            return '';
        }
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime =  $currentUser->wareTimeNotUpdate;

        if ($wareTime== '-12'){
            $timeZone = -12;
        }elseif($wareTime== '-15'){
            $timeZone = -15;
        }else{
            $timeZone = 0;
        }

        $info = date('Y-m-d H:i:s',strtotime($time)+$timeZone*3600);
        return $info;
    }

    /**
     * 用于本地时间查询，创建，编辑
     * CN 0(中国)  USEA -12（美东） USWE -15 (美西)
     * @author zt3361
     */
    public static function opreationTimeZone($time)
    {
        if(isset($action) && !empty($action) ){
            return '';
        }

        //从session获取数据
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime =  $currentUser->wareTime;

        if ($wareTime== '-12'){
            $timeZone = 12;
        }elseif($wareTime== '-15'){
            $timeZone = 15;
        }else{
            $timeZone = 0;
        }

        $info = date('Y-m-d H:i:s',strtotime($time)+$timeZone*3600);
        return $info;
    }
}
