<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 路由模型
 */
class RouteLanguage extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'route_language';

    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'route_language_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 根据路由id获取路由名称
     * @author zt6768
     * @param array $routeIds 路由id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getListByRouteIds($routeIds)
    {
        return self::whereIn('route_id', $routeIds)->get();
    }


}
