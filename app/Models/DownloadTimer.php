<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 路由模型
 */
class DownloadTimer extends Model
{
    const STATUS_EXECUTED = 1;        //已执行
    const STATUS_UNEXECUTED = 0;        //未执行

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'download_timer';



    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * 获取下载定时器中未执行的数据
     * @author zt7242
     * @date 2019/4/24 16:56
     * @param $limit
     * @return mixed
     */
    public static function getDownloadTimerInfo($limit=20)
    {
        return self::where('status',0)->limit($limit)->get()->toArray();
    }

    /**
     * 根据下载id修改下载定时表状态
     * @author zt7242
     * @date 2019/5/15 20:59
     * @param $download_id
     * @param $status
     * @return mixed
     */
    public static function updateStatus($download_id,$status)
    {
        return self::where('download_id',$download_id)->update(['status'=> $status]);
    }

}
