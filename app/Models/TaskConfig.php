<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 路由模型
 */
class TaskConfig extends Model
{

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'task_config';

    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'task_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * 插入配置信息和收件人信息
     * @author zt7242
     * @date 2019/4/26 15:01
     * @param $config
     * @param $consignee
     * @return bool
     */
    public static function insertConfig($config,$consignee)
    {
        $time = date('Y-m-d H:i:s',time());

        DB::beginTransaction();
        //查询数据库是否已经存在该条数据
        $is_exist = self::getConfigInfo();
        if($is_exist){
            $res1 = self::where('task_id',$is_exist->task_id)
                ->update([
                    'is_notice_open'=>$config['is_notice_open'],
                    'remaining_time'=>$config['remaining_time'],
                    'over_time'=>$config['over_time'],
                    'interval_time'=>$config['interval_time'],
                    'frequency'=>$config['frequency'],
                    'message_open'=>$config['message_open'],
                    'message_notice_supplier'=>$config['message_notice_supplier'],
                    'message_notice_content'=>$config['message_notice_content'],
                    'email_open'=>$config['email_open'],
                    'email_notice_supplier'=>$config['email_notice_supplier'],
                    'email_notice_content'=>$config['email_notice_content'],
                    'updated_at'=>$time,
                ]);
        }else{
            $config['created_at'] = $time;
            $config['updated_at'] = $time;
            $res1 = self::insert($config);
        }

        //先清空收件人通知表
        ConsigneeNoticeList::query()->delete();
        //确认表是否已经清空成功
        $count = ConsigneeNoticeList::count();
        //批量插入收件人通知表

        if(!empty($consignee)){

            if($count == 0){
                $res2 = ConsigneeNoticeList::insert($consignee);
            }else{
                $res2 = false;
            }
        }else{
            if($count == 0){
                $res2 = true;
            }else{
                $res2 = false;
            }
        }


        if($res1 && $res2){
            DB::commit();
            return true;
        }else{
            DB::rollback();
            return false;
        }
    }

    /**
     * 获取配置信息
     * @author zt7242
     * @date 2019/4/26 19:39
     * @return mixed
     */
    public static function getConfigInfo()
    {
        return self::get()->first();
    }

}
