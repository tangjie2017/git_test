<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 路由模型
 */
class Download extends Model
{
    const STATUS_PROCESSING = 1;        //处理中
    const STATUS_PROCESSED = 2;        //已处理
    const STATUS_FAIL = 3;        //失败

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'download';

    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'download_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;


    public static function menuList()
    {
        return [
            1=>__('auth.ReservationManagement'),
            2=>__('auth.ReturnCabinetManagement')
        ];
    }

    /**
     * 获取下载列表
     * @author zt7242
     * @date 2019/4/23 14:51
     * @param $data
     * @param $limit
     * @return array
     */
    public static function getDownloadList($data,$limit)
    {
        $query = self::query();
        if(isset($data['download_name'])) {
            $query->where('download_name','like','%'.$data['download_name'].'%');
        }
        if(isset($data['menu_id'])) {
            $query->where('menu_id',$data['menu_id']);
        }
        if(isset($data['time_type']) && isset($data['time_during'])){
            $start_time = substr($data['time_during'],0,19);
            $end_time = substr($data['time_during'],22);

            $start_time = Warehouse::opreationTimeZone($start_time);//zt3361 时区换算
            $end_time = Warehouse::opreationTimeZone($end_time);//zt3361 时区换算

            $type = $data['time_type'] == 1 ? 'created_at':'updated_at';

            if (!empty($start_time)) {
                $query->where($type, '>', $start_time);
            }
            if (!empty($end_time)) {
                $query->where($type, '<', $end_time);
            }
        }



        if(isset($data['status'])) {
            $query->where('status',$data['status']);
        }
        $info = $query->orderBy('created_at','desc')->paginate($limit);
        $count = $info->total();

        return [
            'info' => $info->items(),
            'count' => $count
        ];

    }


    /**
     * 下载删除
     * @author zt7242
     * @date 2019/4/24 11:38
     * @param $id
     * @return mixed
     */
    public static function delDownload($id)
    {
        return self::where('download_id',$id)->delete();
    }

    /**
     * 通过id获取下载信息
     * @author zt7242
     * @date 2019/4/24 14:05
     * @param $id
     * @return mixed
     */
    public static function getDownloadInfo($id)
    {
        return self::find($id);
    }


    /**
     * 将下载地址保存download表中并修改dowmload表状态，修改download_time表状态
     * @author zt7242
     * @date 2019/4/25 11:46
     * @param $filePath
     * @param $download_id
     * @param $downloadStatus
     * @return bool
     */
    public static function updateFileAndStatus($filePath,$download_id,$downloadStatus)
    {
        $status = DownloadTimer::STATUS_EXECUTED;//已执行
        DB::beginTransaction();
        $res1 = self::where('download_id',$download_id)
            ->update(['status'=>$downloadStatus,'file_link'=>$filePath]);
        $res2 = DownloadTimer::updateStatus($download_id,$status);
        if($res1 && $res2){
            DB::commit();
            return true;
        }else{
            DB::rollback();
            return false;
        }
    }
}
