<?php
/**
 * @author zt12700
 * CreateTime: 2019/5/9 17:30
 *
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'role_permission';
    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'role_permission_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 根据用户id和类型获取所有权限
     * @author zt12700
     * @param $id
     * @return mixed
     */
    public static function getPermissionList($id)
    {
        $lan = session()->get('lang') ?? "zh_CN";
        if($lan == 'zh_CN'){
            return RolePermission::leftJoin('route', 'role_permission.route_permission_id', '=', 'route.route_id')
                ->leftJoin('route_language', 'role_permission.route_permission_id', '=', 'route_language.route_id')
                ->where(['role_permission.role_id' => $id])
                ->select('route.route_id as id','parent_route_id as parentId','route_name as title')
                ->orderBy('route.sort')
                ->get()
                ->toArray();
        }else{
            return RolePermission::leftJoin('route', 'role_permission.route_permission_id', '=', 'route.route_id')
                ->leftJoin('route_language', 'role_permission.route_permission_id', '=', 'route_language.route_id')
                ->where(['role_permission.role_id' => $id])
                ->select('route.route_id as id','parent_route_id as parentId','en_name as title')
                ->orderBy('route.sort')
                ->get()
                ->toArray();
        }

    }
}