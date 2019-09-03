<?php
namespace App\Services;
use App\Auth\Common\CurrentUser;
use App\Models\User;
use App\Models\UserWarehouse;
use App\Models\StaticState;

/**
 * 用户绑定服务层
 */
class UserWarehouseService
{
    /**
     * 批量新增或更新用户绑定仓库
     * @author zt6768
     * @param array $data 保存数据
     * @return array
     */
    public static function batchCreateOrUpdate($data)
    {

        $nowTime = date('Y-m-d H:i:s');

        $failData = [];
        $saveUserWarehouse = [];
        $userIdArray = [];
        $i = 0;

        foreach ($data as $userCode => $warehouseCodes) {
            $userWhere = [];
            $userWhere['user_code'] = $userCode;
            $user = User::getInfoByConditon($userWhere);
            //当用户账号信息不存在用户表时,则不用保存用户绑定仓库
            if (empty($user)) {
                $failData['user_code'][] = $userCode;
                continue;
            }

            //加入用户数组
            array_push($userIdArray, $user->user_id);


            foreach ($warehouseCodes[0] as $warehouseCode) {
                if (in_array($warehouseCode,config('api.warehouse')) ) {
                    //收集用户绑定仓库数据
                    $saveUserWarehouse[$i]['user_id'] = $user->user_id;
                    $saveUserWarehouse[$i]['warehouse_code'] = $warehouseCode;
                    $saveUserWarehouse[$i]['warehouse_id'] = 1;
                    $saveUserWarehouse[$i]['warehouse_name'] = $warehouseCode == 'USEA' ? '美东仓库' : '美西仓库';
                    $saveUserWarehouse[$i]['created_at'] = $nowTime;
                    $saveUserWarehouse[$i]['updated_at'] = $nowTime;

                    $i++;
                }
            }
        }


        if ($userIdArray) {
            //先删除用户绑定仓库数据,然后批量新增
            UserWarehouse::deleteByUserIds($userIdArray);
        }

        if ($saveUserWarehouse) {
            //批量新增
            UserWarehouse::insertUserWarehouses($saveUserWarehouse);
        }

        $result = [];
        $result['create_num'] = count($saveUserWarehouse);

        unset($data, $saveUserWarehouse,  $userIdArray);

        return $result;
    }

    /**
     * 获取用户绑定仓库
     * @author zt6768
     */
    public static function getUserWarehouseByUserId()
    {
        $currentUser = CurrentUser::getCurrentUser();
        $userId = $currentUser->userId;

        return UserWarehouse::getUserWarehouseByUserId($userId);
    }

    /**
     * 登录仓库选择
     * @author zt12700
     * @param $user_id
     * @return int|string
     */
    public static function warehouse($user_id)
    {
        $count = UserWarehouse::countUserWarehouses($user_id);
        if($count == 0){
            $timeZone = 8;
            return $timeZone;
        }elseif ($count == 1){
            $info = UserWarehouse::getUserWarehouseByUserId($user_id);
            $warehouse = $info[0]['warehouse_code'];
            if ($warehouse== 'USEA'){
                $timeZone = -12;
            }elseif($warehouse== 'USWE'){
                $timeZone = -15;
            }
            return $timeZone;
        }elseif($count > 1){
            //$warehouse = UserWarehouse::getUserWarehouseByUserId($user_id);
            return 'more';
        }
    }
}