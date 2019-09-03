<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 路由模型
 */
class DownloadCleanup extends Model
{

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'download_cleanup';

    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'cycle_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 获取配置信息
     * @author zt7242
     * @date 2019/4/28 11:39
     * @return mixed
     */
    public static function getCycleInfo()
    {
        return self::get()->first();
    }


    /**
     * 任务配置-删除周期保存
     * @author zt7242
     * @date 2019/4/28 11:44
     * @param $data
     * @return bool
     */
    public static function saveCycleConfig($data)
    {
        $time = date('Y-m-d H:i:s',time());
        //查询数据库是否已经存在该条数据
        $is_exist = self::getCycleInfo();
        if($is_exist){
            $res = self::where('cycle_id',$is_exist->cycle_id)
                ->update([
                    'cleanup_cycle'=>$data['cleanup_cycle'],
                    'updated_at'=>$time,
                ]);
        }else{
            $data['created_at'] = $time;
            $data['updated_at'] = $time;
            $res = self::insert($data);
        }
        if($res){
            return true;
        }else{
            return false;
        }
    }
}
