<?php

namespace App\Services;
use App\Models\Role;
use App\Models\User;

class RoleService
{
    /**
     * @author zt6535
     * CreateTime: 2019/3/12 9:44
     * @param $request
     * @return mixed
     */
    public static function getRoles()
    {
        $result = Role::getRoles();
        return $result;
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
            if (mb_strlen($item['role_name'], 'utf-8') > 30) { //角色名称长度
                $failData[] = $item;
                continue;
            }

            $where = [];
            $where['role_id'] = $item['role_id'];
            $role = Role::getInfoByRole($where);

            if ($role) { //更新数据
                $role->role_name = $item['role_name'];
                $role->updated_at = $nowTime;
                $updateBool = $role->save();
            } else { //收集新增数据
                $item['created_at'] = $nowTime;
                $item['updated_at'] = $nowTime;
                array_push($createData, $item);
            }

            if ($updateBool) { //每更新一条数据自增
                $updateNum++;
            }
        }

        if ($createData) { //新增
            $createBool = Role::batchInsert($createData);
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