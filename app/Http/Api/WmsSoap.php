<?php
namespace App\Http\Api;

use App\Auth\Common\CurrentUser;
use App\Http\Api\Soap\SvcCall;
use App\Models\StaticState;
use App\Services\ApiLogService;
use App\Services\ChannelService;
use App\Services\LogisticsProductsService;
use App\Services\OperatorService;
use App\Models\User;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use App\Models\OrderTimer;
use App\Services\OrderService;
use App\Http\Controllers\Api\TimerApiController;
use Illuminate\Support\Facades\Log;

/**
 * WMS Soap接口方法
 */
class WmsSoap
{

    /**
     * @description 定时将接口获取的仓库数据保存到数据库
     * @author zt7242
     * @date 2019/4/11 13:53
     */
    public static function getWarehouseData()
    {
        $command = 'getWarehouseInfo';
        try{
            $warehouse = [];
            //调用接口
            $result = SvcCall::remoteCommand($command, '');
            Log::info('仓库返回结果: '.json_encode($result));

            if ($result['Message'] == 'Success' && !empty($result['Data'])) {
                $warehouse = $result['Data'];
                unset($result);
            }
            if(isset($warehouse)){
                Warehouse::writeWarehousesTimer($warehouse);
            }else{
                exit('获取失败！');
            }
        }catch(\Exception $e){
            log::info('仓库返回结果:返回异常'.$e);

        }
    }

    /**
     * @description 定时将接口获取的用户仓库数据保存到数据库
     * @author zt7242
     * @date 2019/4/11 14:02
     */
    public static function getUserWarehouseData()
    {
        $command = 'getUserWarehouse';
        $userIds = User::getUserId();
        try {
            $delcountNum = 0;
            $totalcountNum = 0;
            $logs = [];
            $logs['api_type'] = StaticState::API_TYPE_PULL;
            $logs['api_name'] = '获取用户仓库数据';
            $logs['run_start_time'] = date('Y-m-d H:i:s');

            foreach ($userIds as $userId) {
                DB::beginTransaction();
                //通过接口和用户id获取仓库数据
                $params = [];
                $params['user_id'] = $userId;
                $result = SvcCall::remoteCommand($command, $params);

                $isSuccess = isset($result['message']) ? $result['message'] : 0;

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
                    if ($count == 0) {
                        $delcountNum +=$delNum;
                        $totalNum = count($resData);
                        $res = UserWarehouse::insertUserWarehouses($resData);

                        if ($res) {
                            $totalcountNum += $totalNum;
                            DB::commit();
                        } else {
                            DB::rollback();
                            continue;
                        }
                    } else {
                        DB::rollback();
                        continue;
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
            $logs['run_end_time'] = date('Y-m-d H:i:s');
            $logs['is_success'] = 1;
            $logs['request_parameter'] = '';
            $logs['response_result'] = '';
            $logs['handle_situation'] = $handleSituation;
            //记录日志
            ApiLogService::doCreate($logs);
            return 'success';
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return 'fail';
        }

    }


    /**
     * 获取服务商-渠道数据
     * @author zt6768
     * @param int $page 页码
     * @param int $pageSize 每页条数
     * @return string
     */
    public static function getServiceProvider($page = 1, $pageSize = 20)
    {
        $command = 'getServiceProvider';

        //记录日志
        $logs = [];
        $logs['api_type'] = StaticState::API_TYPE_PULL;
        $logs['api_name'] = '获取服务商-渠道数据';
        $logs['run_start_time'] = date('Y-m-d H:i:s');

        $params = [];
        $params['Page'] = $page;
        $params['PageSize'] = $pageSize;
        $result = SvcCall::remoteCommand($command, $params);

        $isSuccess = $result['Ask'] == 'Success' ? 1 : 0;

        $logs['run_end_time'] = date('Y-m-d H:i:s');
        $logs['is_success'] = $isSuccess;
        $logs['request_parameter'] = json_encode($params);
        $logs['response_result'] = json_encode($result);

        if ($isSuccess) {
            $operatorArr = [];
            $channelsArr = [];
            foreach ($result['Data'] as $key => $item) {
                $operatorArr[$key]['operator_id'] = $item['Operator_Id'];
                $operatorArr[$key]['operator_code'] = $item['Operator_Code'];
                $operatorArr[$key]['operator_name'] = $item['Operator_Name'];
                $operatorArr[$key]['operator_status'] = $item['Operator_Status'];

                if ($item['Channel']) {
                    $channel = $item['Channel'];
                    $collectChannel = [];

                    if (isset($channel['Channel_Code'])) { //一维数组
                        $collectChannel['operator_id'] = $item['Operator_Id'];
                        $collectChannel['channel_code'] = $channel['Channel_Code'];
                        $collectChannel['channel_name'] = $channel['Channel_Name'];
                        $collectChannel['channel_status'] = $channel['Channel_Status'];
                        array_push($channelsArr, $collectChannel);
                    } else { //二维数组
                        foreach ($channel as $val) {
                            $collectChannel['operator_id'] = $item['Operator_Id'];
                            $collectChannel['channel_code'] = $val['Channel_Code'];
                            $collectChannel['channel_name'] = $val['Channel_Name'];
                            $collectChannel['channel_status'] = $val['Channel_Status'];
                            array_push($channelsArr, $collectChannel);
                        }
                    }
                }
            }

            $operatorNum = OperatorService::batchCreateOrUpdate($operatorArr);
            $channelNum = ChannelService::batchCreateOrUpdate($channelsArr);

            //接口数据处理情况
            $handleSituation = '';

            //服务商日志
            if ($operatorNum['total_num']) {
                $handleSituation .= '服务商数据获取共：'.$operatorNum['total_num'].'条';
            }

            if ($operatorNum['create_num']) {
                $handleSituation .= ',新增：'.$operatorNum['create_num'].'条';
            }

            if ($operatorNum['update_num']) {
                $handleSituation .= ',编辑：'.$operatorNum['update_num'].'条';
            }

            if ($operatorNum['fail_data']) {
                $logs['fail_data'] = $operatorNum['fail_data'];
                //保存失败数据记录到文件
                Log::error($logs);

                $handleSituation .= ',保存失败：'.count($operatorNum['fail_data']).'条';
            }

            //渠道日志
            if ($channelNum['total_num']) {
                $handleSituation .= ';渠道数据获取共：'.$channelNum['total_num'].'条';
            }

            if ($channelNum['create_num']) {
                $handleSituation .= ',新增：'.$channelNum['create_num'].'条';
            }

            if ($channelNum['update_num']) {
                $handleSituation .= ',编辑：'.$channelNum['update_num'].'条';
            }

            if ($channelNum['fail_data']) {
                $logs['fail_data'] = $channelNum['fail_data'];
                //保存失败数据记录到文件
                Log::error($logs);

                $handleSituation .= ',保存失败：'.count($channelNum['fail_data']).'条';
            }

            $logs['handle_situation'] = $handleSituation;
            //保存记录日志
            ApiLogService::doCreate($logs);

            //分页获取数据
            if ($result['IsNextPage']) {
                $page++;
                self::getServiceProvider($page, $pageSize);
            }

            unset($result);

            return 'success';
        }

        return 'fail';
    }

}