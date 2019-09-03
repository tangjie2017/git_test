<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 接口日志模型
 */
class ApiLog extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'api_log';

    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'api_log_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 查询日志
     */
    public static function getByFilter($request)
    {
        $apiType = $request->api_type;
        $apiName = $request->api_name;
        $isSuccess = $request->is_success;
        $pageSize = 10;

        $where = [];
        if(is_numeric($apiType)){
            $where[] = ['api_type','=',$apiType];
        }

        if(is_numeric($isSuccess)){
            $where[] = ['is_success','=',$isSuccess];
        }

        if($apiName){
            $where[] = ['api_name','like','%'.$apiName.'%'];
        }

        return self::where($where)->orderBy('api_log_id', 'desc')->paginate($pageSize);
    }

    /**
     * 创建日志
     * @author zt6768
     * @param array $data 保存数据
     * @return boolean
     */
    public static function doCreate($data)
    {
        $model = new ApiLog();
        $model->api_type = $data['api_type'];
        $model->api_name = $data['api_name'];
        $model->is_success = isset($data['is_success']) ? $data['is_success'] : 0;
        $model->run_start_time = $data['run_start_time'];
        $model->run_end_time = $data['run_end_time'];
        $model->request_parameter = $data['request_parameter'];
        $model->response_result = $data['response_result'];
        $model->operate_user_id = isset($data['operate_user_id']) ? $data['response_result'] : null;
        $model->operate_user_name = isset($data['operate_user_name']) ? $data['operate_user_name'] : null;
        $model->handle_situation = isset($data['handle_situation']) ? $data['handle_situation'] : null;
        $bool = $model->save();

        if ($bool) {
            return $model->api_log_id;
        }

        return $bool;
    }


}
