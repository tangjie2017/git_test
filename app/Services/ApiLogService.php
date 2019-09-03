<?php

namespace App\Services;
use App\Models\ApiLog;

class ApiLogService
{
    /**
     * 查询日志
     * @param $request
     * @return mixed
     */
    public static function getByFilter($request)
    {
        return ApiLog::getByFilter($request);
    }

    /**
     * 创建日志
     * @author zt6768
     * @param array $data 保存数据
     * @return boolean
     */
    public static function doCreate($data)
    {
        return ApiLog::doCreate($data);
    }
}