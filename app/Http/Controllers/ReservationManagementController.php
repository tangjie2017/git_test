<?php

namespace App\Http\Controllers;

use App\Auth\Common\AjaxResponse;
use App\Auth\Common\CurrentUser;
use App\Auth\Controllers\BaseAuthController;
use App\Http\Api\OWMSService;
use App\Models\InboundOrder;
use App\Models\ReservationManagement;
use App\Models\StaticState;
use App\Models\Warehouse;
use App\Services\ReservationManagementService;
use App\Validates\ReservationCodeValidates;
use App\Validates\ReservationManagementValidates;
use Illuminate\Http\Request;
use Validator;

class ReservationManagementController extends BaseAuthController
{

    /**
     * 预约单管理
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime = $currentUser->wareTimeNotUpdate;
        if($wareTime == '-12'){
            $wareCode = 'USEA';
        }else{
            $wareCode = 'USWE';
        }

        $system = ReservationManagementService::getSystem();//获取系统
        $cabinetType = ReservationManagementService::getCabinetType();//获取柜型
        $source = ReservationManagementService::getSource();//获取来源
        $reservationStatus = ReservationManagementService::getReservationStatus();//获取预约状态
        $status = ReservationManagementService::getStatus();//获取预约主状态
//        $warehouse = ReservationManagementService::getWarehouse();//获取仓库
        //zt3361
        $reservation_number = $request->get('reservation_number') ?? '';
        return view('reservationManagement.index',compact('system','cabinetType','source','reservationStatus','status','wareCode','reservation_number'));
    }

    /**
     * 查询预约单
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $serData = $request->all();
        $requestData = isset($serData['data']) ? $serData['data'] : '';
        $limit = $request->get('limit');

        $data = ReservationManagementService::getDataByFilter($requestData,$limit);

        foreach ($data['info'] as $k=>$v){
            if(isset($v['appointment_delivery_time']) && $v['status'] == StaticState::STATUS_WAIT_SEND_WAREHOUSE){
                $appointment_delivery_time = Warehouse::opreationTimeZone($v['appointment_delivery_time']);
                $today = Warehouse::opreationTimeZone(date('Y-m-d H:i:s'));
                $syts = ceil(((strtotime($appointment_delivery_time) - strtotime($today))/24/3600));
                $data['info'][$k]['syts'] = $syts;

                if($syts <= 0){
                    //预约递送时间如果超时，更新预约状态为已过期
                    ReservationManagement::updateReservationStatus($v['reservation_number_id']);
                }
            }
        }

        $info = array(
            'code' => '0',
            'msg' =>'',
            'count' => $data['count'],
            'data' => $data['info']
        );
        return Response()->json($info);
    }


    /**
     * 接口获取入库单数据
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchInbound(Request $request)
    {
        $serData = $request->all();

        $inbound = new InboundOrder();
        $inboundOrder = $inbound->getInboundData($serData);
        if($inboundOrder){//
            $info = [
                'code' => 404,
                'msg' => __('auth.SingleNumber').':'.$inboundOrder.' '.__("auth.AppointmentOrderHasBeenCreated"),
            ];
            return Response()->json($info);
        }

        $rm = new OWMSService();
        $res = $rm->getInboundOrder($serData['inbound_order_number'],$serData['tracking_number'],$serData['warehouse_code'],$serData['sea_cabinet_number']);//调接口获取

        if(!is_array($res)){
            $info = [
                'code' => 404,
                'msg' => $res,
            ];
            return Response()->json($info);
        }

        $res = $inbound->filterStatus($res);

        $data = [];
        foreach($res as $k=>$v){
            $data[$k]['inbound_order_number'] = $v['receiving_code'];
            $data[$k]['tracking_number'] = $v['tracking_number'];
            $data[$k]['sea_cabinet_number'] = $v['container_number'];
            $data[$k]['customer_code'] = $v['customer_code'];
            $data[$k]['warehouse_code'] = $v['warehouse_code'];
            $data[$k]['warehouse_name'] = $v['warehouse_desc'];
            $data[$k]['products_number'] = $v['sku_total'];
            $data[$k]['sku_species_number'] = $v['sku_species'];
            $data[$k]['box_number'] = $v['box_total'];
            $data[$k]['weight'] = $v['product_weight'];
            $data[$k]['volume'] = $v['product_volume'];
            $data[$k]['created_at'] = $v['receiving_add_time'];
        }
        $count = count($data);
        if($count){
            $info = [
                'code' => '0',
                'data' => $data,
                'count' => $count
            ];
        }else{
            $info = [
                'code' => 404,
                'msg' => __('auth.NoDataFound'),
            ];
        }

        return Response()->json($info);
    }

    /**
     * 编辑预约单
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $id = $request->input('id','');

        if(!$id) abort(404);

        $currentUser = CurrentUser::getCurrentUser();
        $wareTime = $currentUser->wareTimeNotUpdate;
        if($wareTime == '-12'){
            $wareCode = 'USEA';
        }else{
            $wareCode = 'USWE';
        }

        $system = ReservationManagementService::getSystem();//获取系统
        $cabinetType = ReservationManagementService::getCabinetType();//获取柜型
        $source = ReservationManagementService::getSource();//获取来源
        $reservationStatus = ReservationManagementService::getReservationStatus();//获取预约状态
        $status = ReservationManagementService::getStatus();//获取预约主状态
        $containerType = ReservationManagementService::getContainerType();//获取货柜类型
//        $warehouse = ReservationManagementService::getWarehouse();//获取仓库
        $data = ReservationManagementService::getDetail($id);

        return view('reservationManagement.edit',compact('system','cabinetType','containerType','source','reservationStatus','status','data','wareCode'));
    }

    /**
     * 获取编辑页面的入库单数据
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEditInboundOrder(Request $request)
    {
        $data = $request->all();
        $id = $request->input('id','');

        if(!$id) abort(404);

        $rm = new ReservationManagement();
        $data = $rm->getEditInboundOrder($data);

        $info = array(
            'code' => '0',
            'msg' =>'',
            'data' => $data['info'],
            'count' => $data['count']
        );
        return Response()->json($info);

    }

    /**
     * 预约单详情
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request)
    {
        $id = $request->input('id','');

        if(!$id) abort(404);

        $data = ReservationManagementService::getDetail($id);
        $dataFile = ReservationManagementService::getfile($id);
        $dataLog = ReservationManagementService::getLog($id);

        return view('reservationManagement.detail',compact('data','dataFile','dataLog'));

    }

    /**
     * 预约审核预约单
     * @author zt7239
     */
    public function appointmentReview(Request $request)
    {
        $id = $request->input('id','');

        if(!$id) return AjaxResponse::isFailure(__("auth.DidNotGetRequiresAnAppointmentReview"));

        $res = ReservationManagementService::appointmentReview($id);

        if ($res) {
            return AjaxResponse::isSuccess(__("auth.AppointmentReviewSuccessful"));
        } else {
            return AjaxResponse::isFailure(__("auth.AppointmentReviewFailed"));
        }

    }

    /**
     * 废弃预约单
     * @author zt7239
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function discard(Request $request)
    {
        $id = $request->input('id','');

        if(!$id) return AjaxResponse::isFailure(__("auth.DidNotGetAnOrderThatNeedsToBeDiscarded"));

        $res = ReservationManagementService::discard($id);

        if ($res) {
            return AjaxResponse::isSuccess(__("auth.AbandonedSuccessfully"));
        } else {
            return AjaxResponse::isFailure(__("auth.AbandonedFailed"));
        }

    }

    /**
     * 预约单创建页面
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $currentUser = CurrentUser::getCurrentUser();
        $wareTime = $currentUser->wareTimeNotUpdate;
        if($wareTime == '-12'){
            $wareCode = 'USEA';
        }else{
            $wareCode = 'USWE';
        }

        $system = ReservationManagementService::getSystem();//获取系统
        $cabinetType = ReservationManagementService::getCabinetType();//获取柜型
        $source = ReservationManagementService::getSource();//获取来源
        $containerType = ReservationManagementService::getContainerType();//获取货柜类型
//        $warehouse = ReservationManagementService::getWarehouse();//获取仓库

        return view('reservationManagement.create',compact('system','cabinetType','source','containerType','wareCode'));
    }

    /**
     * 审核预约单页面
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function review(Request $request)
    {
        $id =  $request->input('id');
        $res = ReservationManagement::with('InboundOrder')->find($id);

        return view('reservationManagement.review',['res'=>$res]);

    }

    /**
     * 审核预约单更改状态
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateReview(Request $request)
    {
        $requestData = $request->all();
        if(!$requestData['id']) return AjaxResponse::isFailure(__("auth.DidNotGetAnOrderForReview"));
        $validator = Validator::make(
            $requestData,
            ReservationCodeValidates::getRulesAdd(),
            ReservationCodeValidates::getMessages(),
            ReservationCodeValidates::getAttributes()
        );

        if ($validator->fails()) {
            return AjaxResponse::isFailure($validator->errors()->first());
        }

        $bool = ReservationManagementService::updateReview($request);

        if ($bool) {
            return AjaxResponse::isSuccess(__("auth.SuccessfulReview"));
        } else {
            return AjaxResponse::isFailure(__("auth.ReviewFailed"));
        }

    }

    //去掉重复的数组
    function array_unique_fb($array2D,$key)
    {
        $tmp_arr = array();
        foreach ($array2D as $k => $v) {
            if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                unset($array2D[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        sort($array2D); //sort函数对数组进行排序
        return $array2D;
    }

    /**
     * 预约单创建保存
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOrUpdate(Request $request)
    {
        $requestData = $request->all();

        $rm_id = $request->input('reservation_number_id','');
        $inbound_order = $request->input('inbound_order_info','');
        $inbound_order = json_decode($inbound_order,true);

        $arr = array_count_values(array_column($inbound_order, 'inbound_order_number'));

        $arr =  array_filter($arr, function ($value) {
            if ($value > 1) {
                return $value;
            }
        });

        if($arr){
            foreach($arr as $k=>$v){
                $info = array(
                    'code' => '1',
                    'msg' => __('auth.SingleNumber').':'.$k.__("auth.DoNotAddRepeatedly")
                );
                return Response()->json($info);
            }
        }

        $inbound_order = $this->array_unique_fb($inbound_order,'inbound_order_number');//去除重复的数据

        foreach ($inbound_order as $v){
            if($v['warehouse_code'] != $requestData['warehouse_code']){
                $info = array(
                    'code' => '1',
                    'msg' => __('auth.saveDataCorrespondingDestinationWarehouse')
                );
                return Response()->json($info);
            }
        }

        $validaterRes = ReservationManagementService::validaterData($inbound_order,$rm_id,$requestData['warehouse_code']);

        if($validaterRes){
            if(isset($validaterRes['code']) && $validaterRes['code'] == 1){
                $info = array(
                    'code' => '1',
                    'msg' => $validaterRes['msg']
                );
                return Response()->json($info);
            }
        }

        $validator = Validator::make(
            $requestData,
            $rm_id ? ReservationManagementValidates::getRulesUpdate() : ReservationManagementValidates::getRulesCreate(),
            ReservationManagementValidates::getMessages(),
            ReservationManagementValidates::getAttributes()
        );

        if ($validator->fails()) {
            $info = array(
                'code' => '1',
                'msg' => $validator->errors()->first()
            );
            return Response()->json($info);
        }

        if($requestData['earliest_delivery_time'] > $requestData['latest_delivery_time']){
            $info = array(
                'code' => '1',
                'msg' => __("auth.TheEarliestTimeMustLatestPickTime")
            );
            return Response()->json($info);
        }

        $bool = ReservationManagementService::saveData($rm_id,$requestData,$inbound_order);

        if ($bool) {
            $info = array(
                'code' => '0',
                'msg' => __('auth.saveSuccess'),
                'data' => $bool
            );
        } else {
            $info = array(
                'code' => '1',
                'msg' => __('auth.saveFailure')
            );
        }
        return Response()->json($info);
    }

    /**
     * 上传文件
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $file = $request->file('file','');

        if (empty($file)) {
            return AjaxResponse::isFailure(__('auth.pleaseUploadTheFile'));
        }

        if ($file->isValid()) {
            $entension = strtolower($file->getClientOriginalExtension());
            //限制文件格式
            if (!in_array($entension, ['xls','xlsx'])) {
                return AjaxResponse::isFailure(__('auth.FileFormatOnlySupportsXls'));
            }

            //限制大小
            $size = $file->getSize();
            if ($size > config('filesystems.limitFileSize')) {
                return AjaxResponse::isFailure(__('auth.limitFileSize'));
            }
            $name = $file->getClientOriginalName();
            //文件名
//            $fileName = iconv("UTF-8", "GBK", $name);
            $fileName = uniqid().'.'.$entension;;

            //图片上传路径
            $uploadPath = 'uploads/'.date("Y-m-d").'/';
            $movePath = $file->move($uploadPath, $fileName);
            $saveDir = $movePath->getPath();
            $savePath = '/'.$saveDir.'/'.$fileName;

            return AjaxResponse::isSuccess(null, [
                'filePath' => $savePath,
                'name' => $name,
            ]);
        }

        return AjaxResponse::isFailure(__('auth.uploadFailure'));


    }

    /**
     * 导出
     * @author zt7239
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $params = $request->all();

        $validator = Validator::make(
            $params,
            ReservationManagementValidates::downRule(),
            ReservationManagementValidates::getMessages(),
            ReservationManagementValidates::getAttributes()

        );

        if ($validator->fails()) {
            return AjaxResponse::isFailure($validator->errors()->first());
        }

        $res = ReservationManagement::export($params);
        if($res){
            return AjaxResponse::isSuccess(__('auth.ExportTaskAddedSuccessfully'));
        }else{
            return AjaxResponse::isFailure(__('auth.ExportTaskAdditionFailed'));
        }

    }






}
