<?php

namespace App\Http\Controllers\Api;

use App\Http\Api\OWMSService;
use App\Http\Controllers\Controller;
use App\Models\InboundOrder;
use App\Models\ReservationManagement;
use App\Models\StaticState;
use App\Services\ReservationManagementService;
use Illuminate\Http\Request;
use App\Curl\Curl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Validates\ReservationManagementApiValidates;

class ReservationApiController extends Controller
{
    /**
     * 谷仓创建预约单时调用这个接口，我们返回预约单号和预约码
     * @author zt7239
     * @param Request $request
     * @return string
     */
    public function reservationCreate(Request $request)
    {
        $curl = new Curl();
        $return =array(
            'status' => 'Failed',
            'message' => '',
            'response_time' => date('Y-m-d H:i:s')
        );

        $data = $request->all();
        Log::info('创建预约单接收数据: '.json_encode($data));
        if(!$data){
            return json_encode($return);
        }

        $guid = $data['request_id'];
        if($data['sign'] != $curl->sign($guid)){
            $return['message'] = __('auth.signError');
            Log::info('创建预约单接收失败: '.json_encode($return));
            return json_encode($return);
        }
        if(empty($data['request'])){
            $return['message'] = __('auth.NoRequestParameter');
            Log::info('创建预约单接收失败: '.json_encode($return));
            return json_encode($return);
        }

        $res = $data['request'];
        $error = $this->validatesApiData($res,'');
        $error .= $this->validatesInbound($res['inbound_order']);

        if(!empty($error)){
            $return['message'] = $error;
            Log::info('创建预约单接收失败: '.json_encode($return));
            return json_encode($return);
        }

        $arr = array_count_values(array_column($res['inbound_order'], 'inbound_order_number'));

        $arr =  array_filter($arr, function ($value) {
            if ($value > 1) {
                return $value;
            }
        });

        if($arr){
            foreach($arr as $k=>$v){
                $return['message'] = __('auth.SingleNumber').':'.$k.__("auth.DoNotAddRepeatedly");
                Log::info('创建预约单失败: '.json_encode($return));
                return json_encode($return);
            }
        }


        $validaterRes = $this->verifyUsedData($res['inbound_order'],'',$res['warehouse_code']);

        if($validaterRes){
            if(isset($validaterRes['code']) && $validaterRes['code'] == 1){
                $return['message'] = $validaterRes['msg'];
                Log::info('创建预约单接收失败: '.json_encode($return));
                return json_encode($return);
            }
        }

        $file = '';
        if(isset($res['file']) && $res['file']){
            //限制文件格式
            if(isset($res['file_type']) && $res['file_type']){
                if (!in_array($res['file_type'], ['xls','xlsx'])) {
                    $return['message'] = __('auth.FileFormatOnlySupportsXls');
                    Log::info('创建预约单接收失败: '.json_encode($return));
                    return json_encode($return);
                }
            }
            $file = $this->upload_base64_content($res['file'],$res['file_type']);
            if(!$file){
                $return['message'] = __("auth.AttachmentParsingFailed");
                Log::info('创建预约单接收失败: '.json_encode($return));
                return json_encode($return);
            }
        }

        $insertRmArr = [
            'system' => StaticState::SYSTEM_GC_OMS,
            'source' => StaticState::SOURCE_CLIENT,
            'warehouse_code' => $res['warehouse_code'],
            'warehouse_name' => $res['warehouse_name'],
            'customer_code' => $res['customer_code'],
            'type' => $res['type'],
            'cabinet_type' => $res['cabinet_type'],
            'container_type' => $res['container_type'],
            'file' => $file ? $file :'',
            'customs_clearance_time' => $res['customs_clearance_time'],
            'arrival_time' => $res['arrival_time'],
            'earliest_delivery_time' => $res['earliest_delivery_time'],
            'latest_delivery_time' => $res['latest_delivery_time'],
            'operator' => $res['operator'],
            'operating_time' => $res['operating_time'],
            'contact_name' => $res['contact_name'],
            'telephone' => $res['telephone'],
            'email' => $res['email'],
            'created_at' => date('Y-m_d H:i:s'),
        ];

//        $inboundModel = new InboundOrder();
//        $inbData = $inboundModel->filterApiStatus($res['inbound_order']);

        try{
            DB::beginTransaction();
            $reservationNumber = ReservationManagement::reservationNumberCreate();//生成预约单号
            $insertRmArr['reservation_number'] = $reservationNumber;
            $resId = DB::table('reservation_management')->insertGetId($insertRmArr);
            if(!$resId){
                DB::rollback();
                $return = [
                    'status' => 'Failed',
                    'message' => __('auth.CreationFailed')
                ];
                return json_encode($return);
            }

            $insertInboundOrder = array();
            foreach($res['inbound_order'] as $k =>$v){
                $insertInboundOrder[] = [
                    'reservation_number_id' => $resId,
                    'inbound_order_number' => $v['inbound_order_number'],
                    'customer_code' => $v['customer_code'],
                    'tracking_number' => isset($v['tracking_number']) ? $v['tracking_number'] :'',
                    'warehouse_code' => $v['warehouse_code'],
                    'warehouse_name' => $v['warehouse_name'],
                    'products_number' => $v['products_number'],
                    'sea_cabinet_number' => isset($v['sea_cabinet_number']) ? $v['sea_cabinet_number'] : '',
                    'sku_species_number' =>  $v['sku_species_number'],
                    'box_number' => $v['box_number'],
                    'weight' => $v['weight'],
                    'volume' => $v['volume'],
                    'created_at' => $v['created_at'],
                ];
            }

            $inboundOrderId = DB::table('inbound_order')->insert($insertInboundOrder);
            if($inboundOrderId){

                $rmLog[] = [
                    'reservation_number_id' => $resId,
                    'operator_user_name' => $res['operator'],
                    'operator_time' => $res['operating_time'],
                    'operator_type' => StaticState::OPERATOR_TYPE_ADD,
                    'content' => __('auth.NewContent'),
                ];
                DB::table('reservation_management_log')->insert($rmLog);//插入日志

                DB::commit();
                $return['status'] = 'Success';
                $return['reservation_number'] = $reservationNumber;
                Log::info('创建预约单成功: '.$reservationNumber);
                return json_encode($return);

            }else{
                DB::rollback();
                $return = [
                    'status' => 'Failed',
                    'message' =>__('auth.CreationFailed')
                ];
                return json_encode($return);
            }

        }catch (\Exception $e){
            $return['message'] = __('auth.CreationFailed');
            Log::info('创建预约单接收数据异常: '.json_encode($return));
            DB::rollback();
            return json_encode($return);
        }

    }

    /**
     * 校验入库单是否已使用创建预约单
     * @author zt7239
     * @param $inbound_order
     * @param $reservation_number
     * @return array
     */
    public static function verifyUsedData($inbound_order,$reservation_number,$warehouse_code)
    {
        $rm = new OWMSService();
        if(!$reservation_number){//创建
            foreach ($inbound_order as $v){

                $res = $rm->getApiInboundOrder($v['inbound_order_number'],$v['tracking_number'],$warehouse_code,$v['sea_cabinet_number']);//调接口获取

                if(!is_array($res)){
                    return [
                        'code' => 1,
                        'msg' => __('auth.SingleNumber').':'.$v['inbound_order_number'].' '.$res,
                    ];
                }

                $res1 = InboundOrder::filterInboundData($v['inbound_order_number']);

                if($res1){
                    return [
                        'code' => 1,
                        'msg' => __('auth.SingleNumber').':'.$v['inbound_order_number'].' '.__("auth.AppointmentOrderHasBeenCreated")
                    ];
                }
            }
        }else{//编辑
            $collect = new ReservationManagement();
            $collect = $collect->where('reservation_number',$reservation_number);
            $collect = $collect->select('*');
            $collect = $collect->with('InboundOrder')->first()->toArray();

            $inboundOrderData = array_merge($inbound_order, $collect['inbound_order']);
            $inboundOrderData = array_merge($collect['inbound_order'], $inboundOrderData);

            $arr = array_count_values(array_column($inboundOrderData, 'inbound_order_number'));
            $arr = array_filter($arr, function ($value) {
                if ($value < 2) {
                    return $value;
                }
            });

            foreach ($arr as $k => $v) {
                $res = $rm->getApiInboundOrder($k,'',$warehouse_code,'');//调接口获取
                if(!is_array($res)){
                    return [
                        'code' => 1,
                        'msg' => __('auth.SingleNumber').':'.$v['inbound_order_number'].' '.$res,
                    ];
                }

                $res1 = InboundOrder::filterInboundData($v['inbound_order_number']);
                if ($res1) {
                    return [
                        'code' => 1,
                        'msg' => __('auth.SingleNumber') . ':' . $k . ' ' . __("auth.AppointmentOrderHasBeenCreated")
                    ];
                }
            }
        }

    }

    /**
     * 验证字段是否为空
     * @author zt7239
     * @param array $data
     * @return string
     */
    public function validatesApiData($data,$reservation_number)
    {
        $error = '';
        if(isset($data['container_type']) && !empty($data['container_type']) ){
            if($data['container_type'] == 3 || $data['container_type'] == 4){
                if(empty($data['file'])){
                    $error .= __('auth.ContainerTypeIsFBA');
                    return $error;
                }
            }
        }
        if($data['warehouse_code'] != 'USWE' && $data['warehouse_code'] != 'USEA'){
            $error .= __("auth.PleaseChooseWarehouseInTheUSWestOrTheUSEast");
            return $error;
        }
        if(isset($data['inbound_order']) && empty($data['inbound_order'])){
            $error .= __('auth.InboundOrderInformationRequired');
            return $error;
        }
        if(!isset($data['inbound_order'])){
            $error .= __("auth.InboundOrderInformationColumnNotFound");
            return $error;
        }

        if (isset($reservation_number) && $reservation_number){
            $customer_code = ReservationManagement::where('reservation_number',$reservation_number)->first();
            if($customer_code){
                $customer_code = $customer_code->toArray();
                if($customer_code['customer_code'] != $data['customer_code']){
                    $error .= __('auth.CustomerErrorCorresponding');
                    return $error;
                }
            }
        }

        $validator = Validator::make(
            $data,
            $reservation_number? ReservationManagementApiValidates::getRulesUpdate() : ReservationManagementApiValidates::getRulesCreate(),
            ReservationManagementApiValidates::getMessages(),
            ReservationManagementApiValidates::getAttributes()
        );

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        return $error;
    }

    /**
     * 验证入库单号信息
     * @author zt7239
     * @param $inbound_order
     * @return string
     */
    public function validatesInbound($inbound_order)
    {

        foreach ($inbound_order as $value){
            $validator = Validator::make(
                $value,
                ReservationManagementApiValidates::getInboundCreate(),
                ReservationManagementApiValidates::getMessages(),
                ReservationManagementApiValidates::getAttributes()
            );

            if ($validator->fails()) {
                if(isset($value['inbound_order_number'])){
                    return $value['inbound_order_number'].' '.$validator->errors()->first();
                }else{
                    return $validator->errors()->first();
                }

            }
        }
    }

    /**
     * 将base64文件解码保存到本地
     * @author zt7239
     * @param $base64_content
     * @return bool|string
     */
    public function upload_base64_content($base64_content,$file_type){
            //图片上传路径
            $uploadPath = 'uploads/'.date("Y-m-d").'/';
            $type = isset($file_type) ? $file_type : 'xlsx';
            $new_file = $uploadPath;

            if(!file_exists($new_file)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0700);
            }
            $new_file = $new_file.time().".{$type}";

            if (file_put_contents($new_file,base64_decode($base64_content))){
                return '/'.$new_file;
            }else{
                return false;
            }

    }

    /**
     * 编辑OMS的预约单
     * @author zt7239
     * @param Request $request
     * @return string
     */
    public function reservationUpdate(Request $request)
    {
        $curl = new Curl();
        $return =array(
            'status' => 'Failed',
            'message' => '',
            'response_time' => date('Y-m-d H:i:s')
        );

        $data = $request->all();
        Log::info('编辑预约单接收数据: '.json_encode($data));
        if(!$data){
            return json_encode($return);
        }

        $guid = $data['request_id'];
        if($data['sign'] != $curl->sign($guid)){
            $return['message'] = __('auth.signError');
            Log::info('编辑预约单接收失败: '.json_encode($return));
            return json_encode($return);
        }
        if(empty($data['request'])){
            $return['message'] = __('auth.NoRequestParameter');
            Log::info('编辑预约单接收失败: '.json_encode($return));
            return json_encode($return);
        }

        if(empty($data['request']['reservation_number'])){
            $return['message'] = __('auth.reservationNumberRequired');
            Log::info('编辑预约单接收失败: '.json_encode($return));
            return json_encode($return);
        }

        $res = $data['request'];

        $resInfo = ReservationManagement::where('reservation_number',$res['reservation_number'])->first();

        //zt12700
        $re = new ReservationManagement() ;

        $customer = $re->getCustomer($res['reservation_number'],$res['customer_code']);

        if(!$customer){//未查到数据说明不是该客户的
            $return['message'] = __('auth.customerReservationFormWasNotObtained');
            Log::info('编辑预约单接收失败: '.json_encode($return));
            return json_encode($return);
        }


        if(!$resInfo){
            $return['message'] = __('auth.DidNotGetReservationNumber');
            Log::info('编辑预约单接收失败: '.json_encode($return));
            return json_encode($return);
        }else{
            if($resInfo->status != StaticState::STATUS_DRAFT){
                $return['message'] = __("auth.TheOrderStatusCannotEdited");
                Log::info('编辑预约单接收失败: '.json_encode($return));
                return json_encode($return);
            }
        }

        $error = $this->validatesApiData($res,$res['reservation_number']);
        $error .= $this->validatesInbound($res['inbound_order']);

        if(!empty($error)){
            $return['message'] = $error;
            Log::info('编辑预约单接收失败: '.json_encode($return));
            return json_encode($return);
        }

        $arr = array_count_values(array_column($res['inbound_order'], 'inbound_order_number'));

        $arr =  array_filter($arr, function ($value) {
            if ($value > 1) {
                return $value;
            }
        });

        if($arr){
            foreach($arr as $k=>$v){
                $return['message'] = __('auth.SingleNumber').':'.$k.__("auth.DoNotAddRepeatedly");
                Log::info('编辑预约单失败: '.json_encode($return));
                return json_encode($return);
            }
        }

        $reservationNumber = $data['request']['reservation_number'];

        $validaterRes = $this->verifyUsedData($res['inbound_order'],$reservationNumber,$res['warehouse_code']);

        if($validaterRes){
            if(isset($validaterRes['code']) && $validaterRes['code'] == 1){
                $return['message'] = $validaterRes['msg'];
                Log::info('编辑预约单失败: '.json_encode($return));
                return json_encode($return);
            }
        }

        $insertRmArr = [];
        if(isset($res['warehouse_code'])){
            $insertRmArr['warehouse_code'] = $res['warehouse_code'];
        }

        if(isset($res['customer_code'])){
            $insertRmArr['customer_code'] = $res['customer_code'];
        }

        if(isset($res['type'])){
            $insertRmArr['type'] = $res['type'];
        }
        if(isset($res['cabinet_type'])){
            $insertRmArr['cabinet_type'] = $res['cabinet_type'];
        }
        if(isset($res['container_type'])){
            $insertRmArr['container_type'] = $res['container_type'];
        }
        if(isset($res['file']) && isset($res['file_type']) ){
            if($res['file'] && $res['file_type']){
                if (!in_array($res['file_type'], ['xls','xlsx'])) {//限制文件格式
                    $return['message'] = __('auth.FileFormatOnlySupportsXls');
                    Log::info('编辑预约单接收失败: '.json_encode($return));
                    return json_encode($return);
                }

                $file = $this->upload_base64_content($res['file'],$res['file_type']);
                if($file){
                    $insertRmArr['file'] = $file;
                }else{
                    $return['status'] = 'Failed';
                    $return['message'] = __("auth.AttachmentParsingFailed");
                    $return['reservation_number'] = $reservationNumber;
                    return json_encode($return);
                }
            }
        }else if($res['file'] == null){
            $insertRmArr['file'] = '';
        }
        if(isset($res['customs_clearance_time'])){
            $insertRmArr['customs_clearance_time'] = $res['customs_clearance_time'];
        }
        if(isset($res['arrival_time'])){
            $insertRmArr['arrival_time'] = $res['arrival_time'];
        }
        if(isset($res['earliest_delivery_time'])){
            $insertRmArr['earliest_delivery_time'] = $res['earliest_delivery_time'];
        }
        if(isset($res['latest_delivery_time'])){
            $insertRmArr['latest_delivery_time'] = $res['latest_delivery_time'];
        }
        if(isset($res['appointment_delivery_time'])){
            $insertRmArr['appointment_delivery_time'] = $res['appointment_delivery_time'];
        }
        if(isset($res['contact_name'])){
            $insertRmArr['contact_name'] = $res['contact_name'];
        }
        if(isset($res['telephone'])){
            $insertRmArr['telephone'] = $res['telephone'];
        }
        if(isset($res['email'])){
            $insertRmArr['email'] = $res['email'];
        }
        if(isset($res['operator'])){
            $insertRmArr['operator'] = $res['operator'];
        }
        if(isset($res['operating_time'])){
            $insertRmArr['operating_time'] = $res['operating_time'];
        }

//        $inboundModel = new InboundOrder();
//        $inbData = $inboundModel->filterApiStatus($res['inbound_order']);

        $result = ReservationManagement::updateOMSReservationNumberData($reservationNumber,$insertRmArr,$res['inbound_order']);

        if($result){
            if(isset($result['code']) && $result['code'] == 404){
                $return['status'] = 'Failed';
                $return['message'] = $result['msg'];
                $return['reservation_number'] = $reservationNumber;
                return json_encode($return);
            }
            $return['status'] = 'Success';
            $return['reservation_number'] = $reservationNumber;
            return json_encode($return);
        }else{
            $return['status'] = 'Failed';
            $return['message'] = __('auth.EditorFailure');
            $return['reservation_number'] = $reservationNumber;
            return json_encode($return);
        }


    }


    /**
     * 更新谷仓接口过来的预约单状态
     * @author zt7239
     * @param Request $request
     * @return string
     */
    public function updateReservationStatus(Request $request)
    {
        $curl = new Curl();
        $return =array(
            'status' => 'Failed',
            'message' => '',
            'response_time' => date('Y-m-d H:i:s')
        );

        $data = $request->all();
        Log::info('更新预约状态接收数据: '.json_encode($data));
        if(!$data){
            return json_encode($return);
        }

        $guid = $data['request_id'];
        if($data['sign'] != $curl->sign($guid)){
            $return['message'] = __('auth.signError');;
            Log::info('更新预约单状态失败: '.json_encode($return));
            return json_encode($return);
        }
        if(empty($data['request'])){
            $return['message'] = __('auth.NoRequestParameter');
            Log::info('更新预约单状态失败: '.json_encode($return));
            return json_encode($return);
        }

        $res = $data['request'];

        $error = $this->validatesUpdateStatusData($res);

        if($error){
            $return['message'] = $error;
            Log::info('更新预约单状态失败原因: '.json_encode($return));
            return json_encode($return);
        }

        try{
            DB::beginTransaction();
            $count = count($res);
            $i = 0;
            foreach($res as $v){
                $resId = ReservationManagement::updateReservationNumberStatus($v['reservation_number'],$v['status'],$v['operator'],$v['operating_time']);

                if(!$resId){
                    DB::rollback();
                    $return = [
                        'status' => 'Failed',
                        'message' => __('auth.ReservationNumber').':'.$v['reservation_number'].__('auth.StatusUpdateFailed')
                    ];
                    return json_encode($return);
                }else{
                    if(is_array($resId)){
                        DB::rollback();
                        $return = [
                            'status' => 'Failed',
                            'message' => $resId['msg'].':'.$v['reservation_number']
                        ];
                        return json_encode($return);
                    }
                    $i++;
                }
            }

            if($count == $i){
                DB::commit();
                $return['status'] = 'Success';
                $return['message'] = __('auth.updateCompleted');
                return json_encode($return);
            }

        }catch (\Exception $e){
            $return['message'] = __('auth.StatusUpdateFailed');
            Log::info('订单容器关系接收数据异常: '.json_encode($return));
            return json_encode($return);
        }

    }

    /**
     * 更新预约状态时的数据验证
     * @author zt7239
     * @param $data
     * @return string
     */
    public function validatesUpdateStatusData($data)
    {
        $error = '';
        $re = new ReservationManagement() ;
        foreach($data as $v){

            $customer = $re->getCustomer($v['reservation_number'],$v['customer_code']);

            if(!$customer){//未查到数据说明不是该客户的
                $error .= __('auth.customerReservationFormWasNotObtained');
                break;
            }

            $resInfo = ReservationManagement::where('reservation_number',$v['reservation_number'])->first();

            if($resInfo){
                if($resInfo->status != StaticState::STATUS_DRAFT){
                    $error .= __("auth.ReservationNumber").':'.$v['reservation_number'].' '.__("auth.StatusDoesNotMatch");
                    break;
                }
            }

            if(empty($v['reservation_number'])){
                $error .= __('auth.ReservationRequired');
                break;
            }
            if(empty($v['status'])){
                $error .= __('auth.statusRequired');
                break;
            }
            if(empty($v['operator'])){
                $error .= __('auth.operatorRequired');
                break;
            }
            if(empty($v['operating_time'])){
                $error .= __('auth.operatingTimeRequired');
                break;
            }

        }

        return $error;
    }


    /**
     * 谷仓接口查询预约单
     * @author zt7239
     * @param Request $request
     * @return string
     */
    public function searchReservationOrder(Request $request){
        $curl = new Curl();
        $return =array(
            'status' => 'Failed',
            'message' => '',
            'response_time' => date('Y-m-d H:i:s')
        );

        $data = $request->all();
        Log::info('查询预约单接收数据: '.json_encode($data));
        if(!$data){
            return json_encode($return);
        }

        $guid = $data['request_id'];
        if($data['sign'] != $curl->sign($guid)){
            $return['message'] = __('auth.signError');;
            Log::info('查询预约单失败: '.json_encode($return));
            return json_encode($return);
        }

        $res = $data['request'];

        $re = new ReservationManagement() ;

        $info = $re->getOMSReservationOrder($res);

        if($info['to'] == $info['total']){
            $response['IsNextPage'] = false;
        }else{
            $response['IsNextPage'] = true;
        }

        if($info['data']){
            $response['status'] = 'Success';
            $response['count'] = $info['total'];
            $response['data'] = $info['data'];
        }else{
            $response['status'] = 'Failed';
            $response['message'] = __('auth.noData');
        }

        return json_encode($response);

    }

    /**
     * OMS预约单查看详情
     * @author zt7239
     * @param Request $request
     * @return string
     */
    public function reservationNumberDetail(Request $request)
    {
        $curl = new Curl();
        $return =array(
            'status' => 'Failed',
            'message' => '',
            'response_time' => date('Y-m-d H:i:s')
        );

        $data = $request->all();
        Log::info('查看预约单详情请求数据: '.json_encode($data));
        if(!$data){
            return json_encode($return);
        }

        $guid = $data['request_id'];
        if($data['sign'] != $curl->sign($guid)){
            $return['message'] = __('auth.signError');;
            Log::info('查看预约单详情失败: '.json_encode($return));
            return json_encode($return);
        }

        if(empty($data['request'])){
            $return['message'] = __('auth.NoRequestParameter');
            Log::info('查看预约单详情失败: '.json_encode($return));
            return json_encode($return);
        }

        $res = $data['request'];

        if(empty($res['reservation_number'])){
            $return['message'] = __('auth.NoReservationNumber');
            Log::info('查看预约单详情失败: '.json_encode($return));
            return json_encode($return);
        }

        if(empty($res['customer_code'])){
            $return['message'] = __('auth.CustomerCodeNotFound');
            Log::info('查看预约单详情失败: '.json_encode($return));
            return json_encode($return);
        }

        $re = new ReservationManagement() ;

        $customer = $re->getCustomer($res['reservation_number'],$res['customer_code']);

        if(!$customer){//未查到数据说明不是该客户的
            $return['message'] = __('auth.customerReservationFormWasNotObtained');
            Log::info('查看预约单详情失败: '.json_encode($return));
            return json_encode($return);
        }

        $info = $re->getOMSReservationOrderDetail($res);

        if($info){
            if($info['file']){
                $info['file_type'] = substr($info['file'],strripos($info['file'],".")+1);
                if(file_get_contents(asset($info['file']))){
                    $info['file'] = base64_encode(file_get_contents(asset($info['file'])));
                }else{
                    $response['status'] = 'Failed';
                    $response['message'] = __('auth.AttachmentNotFound');
                    return json_encode($response);
                }
            }
            $response['status'] = 'Success';
            $response['data'] = $info;
        }else{
            $response['status'] = 'Failed';
            $response['message'] = __('auth.noData');
        }
//        dd($info);
        return json_encode($response);


    }

}