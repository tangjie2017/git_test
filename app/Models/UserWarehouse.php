<?php
/**
 * @author zt12700
 * CreateTime: 2019/4/28 16:20
 *
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserWarehouse extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_warehouse';
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
     * @description 根据用户id删除用户仓库数据
     * @author zt12700
     * @date 2019/4/4 15:13
     * @param $user_id
     * @return mixed
     */
    public static function deleteUserWarehouses($user_id)
    {
        return self::where('user_id', $user_id)->delete();
    }

    /**
     * @description 统计用户仓库数据
     * @author zt12700
     * @date 2019/4/4 15:13
     * @param $user_id
     * @return mixed
     */
    public static function countUserWarehouses($user_id)
    {
        return self::where('user_id',$user_id)->count();
    }

    /**
     * 批量插入用户仓库数据
     * @author zt12700
     * @param array $data
     * @return boolean
     */
    public static function insertUserWarehouses($data)
    {
        $model = new UserWarehouse();
        return $model->insert($data);
    }

    /**
     * 根据用户id删除用户绑定仓库
     * @param array $userIds 用户id
     * @return boolean
     */
    public static function deleteByUserIds($userIds)
    {
        return self::whereIn('user_id', $userIds)->delete();
    }

    /**
     * 获取用户绑定仓库
     * @author zt12700
     * @param int $userId 用户id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getUserWarehouseByUserId($userId)
    {
        return self::where('user_id', $userId)->get();
    }


}