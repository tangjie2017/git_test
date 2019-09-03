<?php

namespace App\Http\Api;

use App\Models\StaticState;
use App\Services\ApiLogService;
use App\Services\RoleService;
use App\Services\UserService;
use App\Services\UserWarehouseService;
use App\Http\Api\Soap\SvcCall;
use Dompdf\Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\UserWarehouse;
use Illuminate\Support\Facades\DB;
use App\Curl\Curl;

/**
 * Wms和Oms系统接口
 * Class WmsApi
 */
class WmsOmsApi
{
    /**
     * 获取fbg用户信息
     * @author zt12700
     * @return array
     */
    public static function getWmsUserData($PageNumber = 1,$PageSize = 20)
    {
        $curl = new Curl();

        $requestData = [];
        $requestData['PageNumber'] = $PageNumber;
        $requestData['PageSize'] = $PageSize;

        $openLoginUrl = config('api.fbg.openLogin');

        //登录接口参数
        $openLoginData = [];
        $openLoginData['UserCode'] = config('api.fbg.UserCode');
        $openLoginData['Password'] = config('api.fbg.Password');
        $openLoginData['AppId'] = config('api.fbg.appId');
        //通过登录接口获取token
        $result = $curl->vpost($openLoginUrl, $openLoginData);

        if (substr($result['code'],0,1) == 2) {
            $loginReponse = json_decode($result['data'], true);
            $token = $loginReponse['ResponseData']['AccessToken'];

            $getUserUrl = config('api.fbg.getUser');
            $res = $curl->postFBG($getUserUrl,$requestData,$token);
            if (substr($res['code'],0,1) == 2) {
                $getUserReponse = json_decode($res['data'], true);

                //收集用户信息
                $userArray = [];
                foreach ($getUserReponse['ResponseData']['Data'] as $item){
                    //收集用户数据
                    if (!array_key_exists($item['UserCode'], $userArray)) {
                        $userArray[] = $item['UserId'];
                    }
                }

                //调用wms获取接口
                echo self::getWmsUser($userArray);
                echo "\r\n";
                if($getUserReponse['ResponseData']['HasNextPage'] == 'true'){
                    $PageNumber++;
                    self::getWmsUserData($PageNumber);
                }

            }else{
                Log::error($res);

                return 'fail';
            }

        }else{
            Log::error($result);

            return 'fail';
        }


    }


    /**
     * 获取用户信息接口（包含用户绑定仓库）
     * @author zt12700
     * @param int $page
     * @param int $pageSize
     * @return string
     */
    public static function getWmsUser($userArray)
    {
        $curl = new Curl();
        $guid = $curl->create_guid();

        if (config('api.app_model')) {
            $url = config('api.fbg.lineWmsUser');
        } else {
            $url = config('api.wmsOms.newWmsUser');
        }

        $requestData['user_ids'] = $userArray;
        $time = time();
        $requestParam = [];

        $requestParam['request_id'] = $guid;
        $requestParam['request_time'] = $time;
        $requestParam['language'] =session()->get('lang');
        $requestParam['app_code'] = 'bis';
        $requestParam['sign'] = $curl->signWms($time,$requestData);
        $requestParam['request_data'] = $requestData;

        //记录日志
        $logs = [];
        $logs['api_type'] = StaticState::API_TYPE_PULL;
        $logs['api_name'] = '获取用户信息接口';
        $logs['run_start_time'] = date('Y-m-d H:i:s');

        $result = $curl->vpost($url, $requestParam);

        $logs['run_end_time'] = date('Y-m-d H:i:s');
        $logs['request_parameter'] = json_encode($requestParam);
        $logs['response_result'] = json_encode($result);

        if (substr($result['code'],0,1) == 2) {
            $responseData = json_decode($result['data'], true);

            $ask = $responseData['ask'];
            $isSuccess = $ask == 'Success' ? 1 : 0;

            $data = $responseData['data']['list'];
            $totalData = count($data);
            if ($ask == 'Failure') {
                //如果响应成功&响应数据为空，则表示响应成功
                if (empty($data)) {
                    //接口状态
                    $logs['is_success'] = 1;
                    //保存日志
                    ApiLogService::doCreate($logs);

                    return 'success';
                }

                //接口状态
                $logs['is_success'] = 0;
                //保存日志
                ApiLogService::doCreate($logs);

                return 'fail';
            }

            //接口状态
            $logs['is_success'] = $isSuccess;


            $userArray = [];
            $roleArray = [];
            $userWarehouseArray = [];
            foreach ($data as $item) {
                //收集用户数据
                if (!array_key_exists($item['user_code'], $userArray)) {
                    $userArray[$item['user_code']] = [
                        'user_id' => $item['user_id'],
                        'user_code' => $item['user_code'],
                        'role_id' => $item['role_id'],
                        'status' => $item['user_status'],
                        'password' => Hash::make($item['user_code'])
                    ];
                }

                //收集角色数据
                if (!array_key_exists($item['role_id'], $roleArray)  && isset($item['role_name']) ) {
                    $roleArray[$item['role_id']] = [
                        'role_id' => $item['role_id'],
                        'role_name' => $item['role_name'],
                    ];
                }



                //收集用户绑定仓库数据
                $userWarehouseArray[$item['user_code']][] = $item['warehouse_list'];

            }

            $userNum = UserService::batchCreateOrUpdate($userArray);
            $roleNum = RoleService::batchCreateOrUpdate($roleArray);
            $userWarehouseNum = UserWarehouseService::batchCreateOrUpdate($userWarehouseArray);

            //接口数据处理情况
            $handleSituation = '';

            //用户数据日志
            if ($userNum['total_num']) {
                $handleSituation .= '用户数据获取共：' . $userNum['total_num'] . '条';
            }

            if ($userNum['create_num']) {
                $handleSituation .= ',新增：' . $userNum['create_num'] . '条';
            }

            if ($userNum['update_num']) {
                $handleSituation .= ',编辑：' . $userNum['update_num'] . '条';
            }

            if ($userNum['fail_data']) {
                $logs['fail_data'] = $userNum['fail_data'];
                //保存失败数据记录到文件
                Log::error($logs);

                $handleSituation .= ',保存失败：' . count($userNum['fail_data']) . '条';
            }

            //角色数据日志
            if ($roleNum['total_num']) {
                $handleSituation .= ';角色数据获取共：' . $roleNum['total_num'] . '条';
            }

            if ($roleNum['create_num']) {
                $handleSituation .= ',新增：' . $roleNum['create_num'] . '条';
            }

            if ($roleNum['update_num']) {
                $handleSituation .= ',编辑：' . $roleNum['update_num'] . '条';
            }

            if ($roleNum['fail_data']) {
                $logs['fail_data'] = $userNum['fail_data'];
                //保存失败数据记录到文件
                Log::error($logs);

                $handleSituation .= ',保存失败：' . count($roleNum['fail_data']) . '条';
            }

            //用户绑定仓库日志
            if ($userWarehouseNum['create_num']) {
                $handleSituation .= ';用户绑定仓库新增：'.$roleNum['create_num'].'条';
            }


            $logs['handle_situation'] = $handleSituation;
            //保存日志
            ApiLogService::doCreate($logs);

            Log::info($logs);
            return 'success';
        } else {
            $logs['is_success'] = 0;
            //保存日志
            ApiLogService::doCreate($logs);

            return 'fail';
        }
    }

    /**
     * @description 定时将接口获取的用户仓库数据保存到数据库
     * @author zt12700
     * @date 2019/5/27 14:02
     */
    public static function getUserWarehouseData()
    {
        $command = 'getUserWarehouse';
        $userIds = User::getUserId();
        DB::beginTransaction();
        try{
            $delcountNum = 0;
            $totalcountNum = 0;
            foreach($userIds as $userId){
                //通过接口和用户id获取仓库数据
                $logs = [];
                $logs['api_type'] = StaticState::API_TYPE_PULL;
                $logs['api_name'] = '获取用户仓库数据';
                $logs['run_start_time'] = date('Y-m-d H:i:s');

                $params = [];
                $params['user_id'] = $userId;
                $result = SvcCall::remoteCommand($command, $params);
                $isSuccess = isset($result['message']) ? $result['message'] : 0;

                $logs['run_end_time'] = date('Y-m-d H:i:s');
                $logs['is_success'] = $isSuccess == 'Success' ? 1 : 0;
                $logs['request_parameter'] = json_encode($params);
                $logs['response_result'] = json_encode($result);

                if ($isSuccess == 'Success') {

                    $time = date('Y-m-d H:i:s');
                    $resData = [];
                    foreach ($result['data'] as $v) {
                        if (in_array($v['warehouse_code'],config('api.warehouse')) ) {
                            $resData[] = [
                                'warehouse_id' => 1,
                                'user_id' => $userId,
                                'warehouse_code' => $v['warehouse_code'],
                                'warehouse_name' => $v['warehouse_desc'],
                                'created_at' => $time,
                                'updated_at' => $time
                            ];
                        }
                    }
                    $delNum = UserWarehouse::deleteUserWarehouses($userId);
                    $count = UserWarehouse::countUserWarehouses($userId);
                    //判断是否删除成功
                    if($count == 0){
                        $delcountNum +=$delNum;
                        $totalNum = count($resData);
                        $res = UserWarehouse::insertUserWarehouses($resData);

                        if($res){
                            $totalcountNum += $totalNum;
                            DB::commit();
                        }else{
                            DB::rollback();
                            break;

                        }
                    }else{
                        DB::rollback();
                        break;

                    }
                }

            }

            //接口数据处理情况
            $handleSituation = '';

            if ($delcountNum) {
                $handleSituation .= '用户仓库共删除：'.$delcountNum.'条';
            }
            if ($totalcountNum) {
                $handleSituation .= ',新增：'.$totalcountNum.'条';
            }

            $logs['handle_situation'] = $handleSituation;
            //记录日志
            ApiLogService::doCreate($logs);
            return 'success';
        }catch(\Exception $e){
            DB::rollback();
            exit($e->getMessage());
        }

    }

}
