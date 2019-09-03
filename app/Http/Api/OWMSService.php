<?php
namespace App\Http\Api;

use App\Curl\Curl;
use App\Http\Api\Soap\SvcCall;
use App\Models\InboundOrder;
use App\Models\StaticState;
use App\Services\ApiLogService;
use App\Services\RoleService;
use App\Services\UserService;
use App\Services\UserWarehouseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Auth\Common\AjaxResponse;
/**
 * OWMS service 接口
 */
class OWMSService
{
    /**
     * 入库单接口
     * @author zt7242
     * @date 2019/5/17 15:32
     * @param $inbound_order_number
     * @param $tracking_number
     * @param $warehouse_code
     * @param $sea_cabinet_number
     * @param int $page
     * @param int $pageSize
     * @return array|string
     */
    public static function getInboundOrder($inbound_order_number,$tracking_number,$warehouse_code,$sea_cabinet_number,$page = 1,$pageSize = 10)
    {
        if(empty($inbound_order_number) && empty($tracking_number) && empty($sea_cabinet_number)) {
            return __('auth.PleaseEnterQueryCriteria');
        }

        $curl = new Curl();
        $guid = $curl->create_guid();

        $requestData = [];
        $requestData['receiving_code'] = $inbound_order_number??'';
        $requestData['tracking_number'] = $tracking_number??'';
        $requestData['container_number'] = $sea_cabinet_number??'';
        $requestData['page'] = $page;
        $requestData['pageSize'] = $pageSize;

        if(config('api.app_model')){
            $url = config('api.wmsOms.inboundUrl');
        }else{
            $url = config('api.wmsOms.testInboundUrl');

        }

        $time = time();
        $requestParam = [];

        $requestParam['request_id'] = $guid;
        $requestParam['request_time'] = $time;
        $requestParam['language'] = session()->get('lang');
        $requestParam['app_code'] = 'bis';
        $requestParam['sign'] = $curl->signWms($time,$requestData);
        $requestParam['request_data'] = $requestData;
//dd(json_encode($requestParam));
        //记录请求参数
        Log::info('入库单请求参数: '.json_encode($requestParam));
        //请求接口

        $result = $curl->vpost($url, $requestParam);

        //记录返回参数
        Log::info('入库单返回参数: '.json_encode($result));
        //接口请求通过
        if (substr($result['code'],0,1) == 2) {
            $responseData = json_decode($result['data'], true);
            $ask = $responseData['ask'];
            $data = $responseData['data'];
            if ($ask == 'Failure' || empty($data)) {
                return __('auth.queryFailed');
            }

            $list = $data['list'];

            if(!$list){
                return __('auth.fillTheCorrectOrderNumber');
            }

            foreach($list as $k=>$v){
                //过滤掉非在途状态的
                if($v['receiving_status'] != 5){
                    unset($list[$k]);
                }
                //过滤掉已签收的
                if($v['sign_status'] == 1){
                    unset($list[$k]);
                }
                //过滤掉非非选择的目的仓
                if($v['warehouse_code'] != $warehouse_code){
                    unset($list[$k]);
                }
                //过滤掉货运方式只能为海运散货和海运整柜
                if($v['receiving_shipping_type'] != 1 && $v['receiving_shipping_type'] != 4){
                    unset($list[$k]);
                }
            }

            if (!empty($data['has_next_page'])) {
                $page++;
                $listMore = self::getInboundOrder($inbound_order_number,$tracking_number,$warehouse_code,$sea_cabinet_number,$page,$pageSize);
                if(is_array($listMore)){
                    $merge = array_merge($list,$listMore);
                    return $merge;
                }else{
                    return $listMore;
                }
            }else{
                return $list;
            }
        }

        return __('auth.queryFailed');
    }

    /**
     * 调接口时验证创建和编辑的入库单是否符合
     * @author zt7239
     * @param $inbound_order_number
     * @param $tracking_number
     * @param $warehouse_code
     * @param $sea_cabinet_number
     * @param int $page
     * @param int $pageSize
     * @return array|null|string
     */
    public static function getApiInboundOrder($inbound_order_number = '',$tracking_number ='',$warehouse_code='',$sea_cabinet_number='',$page = 1,$pageSize = 10)
    {
//        if(empty($inbound_order_number) && empty($tracking_number)) {
//            return __('auth.passLeastOne');
//        }

        $curl = new Curl();
        $guid = $curl->create_guid();

        $requestData = [];
        $requestData['receiving_code'] = $inbound_order_number??'';
        $requestData['tracking_number'] = $tracking_number??'';
        $requestData['container_number'] = $sea_cabinet_number??'';
        $requestData['page'] = $page;
        $requestData['pageSize'] = $pageSize;

        if(config('api.app_model')){
            $url = config('api.wmsOms.inboundUrl');
        }else{
            $url = config('api.wmsOms.testInboundUrl');

        }

        $time = time();
        $requestParam = [];

        $requestParam['request_id'] = $guid;
        $requestParam['request_time'] = $time;
        $requestParam['language'] = session()->get('lang');
        $requestParam['app_code'] = 'bis';
        $requestParam['sign'] = $curl->signWms($time,$requestData);
        $requestParam['request_data'] = $requestData;

        //记录请求参数
        Log::info('入库单请求参数: '.json_encode($requestParam));
        //请求接口

        $result = $curl->vpost($url, $requestParam);

        //记录返回参数
        Log::info('入库单返回参数: '.json_encode($result));
        //接口请求通过
        if (substr($result['code'],0,1) == 2) {
            $responseData = json_decode($result['data'], true);
            $ask = $responseData['ask'];
            $data = $responseData['data'];
            if ($ask == 'Failure' || empty($data)) {
                return __('auth.queryFailed');
            }

            $list = $data['list'];

            if(!$list){
                return ' '.__('auth.fillTheCorrectOrderNumber');
            }

            foreach($list as $k=>$v){
                //过滤掉非在途状态的
                if($v['receiving_status'] != 5){
                    return __('auth.StatusDoesNotMatch');
                }
                //过滤掉已签收的
                if($v['sign_status'] == 1){
                    return __('auth.HaveBeenReceived');
                }
                //过滤掉非非选择的目的仓
                if($v['warehouse_code'] != $warehouse_code){
                    return __('auth.DestinationBinDoesNotMatch');
                }
                //过滤掉货运方式只能为海运散货和海运整柜
                if($v['receiving_shipping_type'] != 1 && $v['receiving_shipping_type'] != 4){
                    return __("auth.shippingMethodOnlySupportsSeaborneAndSeaFreight");
                }
            }
            if (!empty($data['has_next_page'])) {
                $page++;
                $listMore = self::getInboundOrder($inbound_order_number,$tracking_number,$warehouse_code,$page,$pageSize);
                if(is_array($listMore)){
                    $merge = array_merge($list,$listMore);
                    return $merge;
                }else{
                    return $listMore;
                }
            }else{
                return $list;
            }
        }

        return __('auth.queryFailed');
    }

    /**
     * 获取谷仓用户信息
     * @author zt6768
     * @param int $page 页码
     * @param int $pageSize 每页条数
     * @return string
     */
    public static function userInfo($page = 1, $pageSize = 200)
    {
        $curl = new Curl();
        $guid = $curl->create_guid();

        $requestData = [];
        $requestData['page'] = $page;
        $requestData['pageSize'] = $pageSize;

        if (config('api.app_model')) {
            $url = config('api.wmsOms.authUrl');
        } else {
            $url = config('api.wmsOms.testAuthUrl');
        }

        $requestParam = [];
        $requestParam['version'] = '1.0.0';
        $requestParam['request_time'] = date('Y-m-d H:i:s');
        $requestParam['request_id'] = $guid;
        //用户信息
        $requestParam['service'] = 'getUserInfo';
        $requestParam['sign'] = $curl->sign($guid);
        $requestParam['url'] = $url;
        $requestParam['lang'] = session()->get('lang');
        $requestParam['request'] = $requestData;

        //记录日志
        $logs = [];
        $logs['api_type'] = StaticState::API_TYPE_PULL;
        $logs['api_name'] = '获取用户信息接口';
        $logs['run_start_time'] = date('Y-m-d H:i:s');

        //请求接口
        $result = $curl->vpost($url, $requestParam);

        $logs['run_end_time'] = date('Y-m-d H:i:s');
        $logs['request_parameter'] = json_encode($requestParam);
        $logs['response_result'] = json_encode($result);

        //接口请求通过
        if (substr($result['code'],0,1) == 2) {
            $responseData = json_decode($result['data'], true);

            $ask = $responseData['ask'];
            $isSuccess = $ask == 'Success' ? 1 : 0;

            $data = $responseData['data'];
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
                        'user_code' => $item['user_code'],
                        'role_id' => $item['up_id'],
                        'status' => $item['user_status'],
                        'password' => Hash::make($item['user_code'])
                    ];
                }

                //收集角色数据
                if (!array_key_exists($item['up_id'], $roleArray)) {
                    $roleArray[$item['up_id']] = [
                        'role_id' => $item['up_id'],
                        'role_name' => $item['up_name'],
                    ];
                }

                //收集用户绑定仓库数据
                $userWarehouseArray[$item['user_code']][] = $item['warehouse_code'];
            }

            $userNum = UserService::batchCreateOrUpdate($userArray);
            $roleNum = RoleService::batchCreateOrUpdate($roleArray);
            $userWarehouseNum = UserWarehouseService::batchCreateOrUpdate($userWarehouseArray);

            //接口数据处理情况
            $handleSituation = '';

            //用户数据日志
            if ($userNum['total_num']) {
                $handleSituation .= '用户数据获取共：'.$userNum['total_num'].'条';
            }

            if ($userNum['create_num']) {
                $handleSituation .= ',新增：'.$userNum['create_num'].'条';
            }

            if ($userNum['update_num']) {
                $handleSituation .= ',编辑：'.$userNum['update_num'].'条';
            }

            if ($userNum['fail_data']) {
                $logs['fail_data'] = $userNum['fail_data'];
                //保存失败数据记录到文件
                Log::error($logs);

                $handleSituation .= ',保存失败：'.count($userNum['fail_data']).'条';
            }

            //角色数据日志
            if ($roleNum['total_num']) {
                $handleSituation .= ';角色数据获取共：'.$roleNum['total_num'].'条';
            }

            if ($roleNum['create_num']) {
                $handleSituation .= ',新增：'.$roleNum['create_num'].'条';
            }

            if ($roleNum['update_num']) {
                $handleSituation .= ',编辑：'.$roleNum['update_num'].'条';
            }

            if ($roleNum['fail_data']) {
                $logs['fail_data'] = $userNum['fail_data'];
                //保存失败数据记录到文件
                Log::error($logs);

                $handleSituation .= ',保存失败：'.count($roleNum['fail_data']).'条';
            }

            //用户绑定仓库日志
            if ($userWarehouseNum['create_num']) {
                $handleSituation .= ';用户绑定仓库新增：'.$roleNum['create_num'].'条';
            }

            if ($userWarehouseNum['fail_data']) {
                if (isset($userWarehouseNum['user_code'])) {
                    $handleSituation .= ';账号共：'.count($userWarehouseNum['fail_data']['user_code']).'条不存在用户表';
                }

                if (isset($userWarehouseNum['warehouse_code'])) {
                    $handleSituation .= ';仓库代码共：'.count($userWarehouseNum['fail_data']['warehouse_code']).'条不存在仓库表';
                }

                $logs['fail_data'] = $userNum['fail_data'];
                //保存失败数据记录到文件
                Log::error($logs);
            }

            $logs['handle_situation'] = $handleSituation;
            //保存日志
            ApiLogService::doCreate($logs);

            //分页获取数据
            if ($totalData >= $pageSize) {
                $page++;
                self::userInfo($page);
            }

            return 'success';
        }

        //保存日志
        ApiLogService::doCreate($logs);

        return 'fail';
    }



}