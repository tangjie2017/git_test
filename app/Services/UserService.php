<?php

namespace App\Services;
use App\Models\User;
use App\Models\Role;
use App\Models\StaticState;

class UserService
{

    /**
     * @author zt6535
     * CreateTime: 2019/3/12 9:44
     * @param $request
     * @return mixed
     */
    public function getByFilter($request )
    {
        $model = new User();
        $result = $model->filter($request);

        return $result;
    }

    /**
     * 获取角色
     */
    public static function getRole()
    {

        $role = Role::where('status',1)->get();
        return $role;
    }

    /**
     * 用户启用状态
     * @author zt6535
     * CreateTime: 2019/3/12 13:44
     * @return array
     */
    public static function getStatus()
    {
        return User::getStatus();
    }

    /**
     * 根据条件验证用户
     * @author zt6768
     * @param array $conditon 条件
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getInfoByConditon($conditon)
    {
        return User::getInfoByConditon($conditon);
    }

    /**
     * 新增用户
     * @author zt6768
     * @param array $data 数据
     * @return boolean
     */
    public static function doCreate($data)
    {
        return User::doCreate($data);
    }

    /**
     * 批量新增或更新用户
     * @author zt6768
     * @param array $data 保存数据
     * @return array
     */
    public static function batchCreateOrUpdate($data)
    {
        $nowTime = date('Y-m-d H:i:s');

        $createData = [];
        $updateNum = 0;
        $createNum = 0;
        $createBool = false;
        $updateBool = false;
        $totalNum = count($data);
        $failData = [];
        foreach ($data as $item) {
            if (mb_strlen($item['user_code'], 'utf-8') > 50) { //账号长度
                $failData[] = $item;
                continue;
            }

            $where = [];
            $where['user_code'] = $item['user_code'];
            $user = User::getInfoByConditon($where);

            if ($user) { //更新数据
                $user->user_id = $item['user_id'];
                $user->role_id = $item['role_id'];
                $user->updated_at = $nowTime;
                $updateBool = $user->save();
            } else { //收集新增数据
                $item['updated_at'] = $nowTime;
                $item['created_at'] = $nowTime;
                array_push($createData, $item);
            }

            if ($updateBool) { //每更新一条数据自增
                $updateNum++;
            }
        }

        if ($createData) { //新增
            $createBool = User::batchInsert($createData);
        }

        if ($createBool) { //新增数量
            $createNum = count($createData);
        }

        $result = [];
        $result['create_num'] = $createNum;
        $result['update_num'] = $updateNum;
        $result['total_num'] = $totalNum;
        $result['fail_data'] = $failData;

        unset($data, $createData);

        return $result;
    }

}