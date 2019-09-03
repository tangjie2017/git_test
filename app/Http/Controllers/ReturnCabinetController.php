<?php

namespace App\Http\Controllers;

use App\Auth\Common\CurrentUser;
use App\Auth\Controllers\BaseAuthController;
use App\Services\ReturnCabinetService;
use App\Validates\ReturnCabinetValidates;
use App\Models\ReturnCabinet;
use Validator;
use Illuminate\Http\Request;
use App\Auth\Common\AjaxResponse;

class ReturnCabinetController extends BaseAuthController
{
    /**
     * 还柜单管理
     * @author zt12700
     * CreateTime: 2019/4/23 9:06
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

        $system = ReturnCabinetService::getSystem(); //获取系统
        $cabinetType = ReturnCabinetService::getCabinetType();//获取柜型
        $source = ReturnCabinetService::getSource();//获取来源
        $status = ReturnCabinetService::getStatus();//获取状态
//        $warehouse = ReturnCabinetService::getWarehouse();//获取仓库

        return view('returnCabinet.index',compact('system','cabinetType','source','status','wareCode'));
    }

    /**
     * 还柜搜索
     * @author zt12700
     */
    public function search(Request $request)
    {
        $info = $request->all();
        $data = isset($info['data']) ? $info['data'] : '';
        $limit = $info['limit'];
        $download = ReturnCabinetService::getList($data,$limit);

        $res = array(
            'code' => '0',
            'msg' =>'',
            'count' => $download['count'],
            'data' => $download['info']
        );
//        dd($res);
        return Response()->json($res);
    }

    /**
     * 还柜单查看
     * @author zt12700
     * CreateTime: 2019/4/23 9:06
     */
    public function look(Request $request)
    {
        $id = $request->input('id');
        $res = ReturnCabinet::with('rem')->with('inbound')->with('file')->with('log')->find($id);
//        dd($res);
        return view('returnCabinet.look',['res'=>$res]);

    }


    /**
     * 还柜单邮件发送页面
     * @author zt12700
     * CreateTime: 2019/4/26 9:06
     */
    public function emiltext(Request $request)
    {
        $id = $request->input('id');
        $res = ReturnCabinet::with('rem')->find($id);
        return view('returnCabinet.emil',['res'=>$res]);
    }

    /**
     * 还柜单邮件发送
     * @author zt12700
     * CreateTime: 2019/4/26 9:16
     */
    public function emil(Request $request)
    {
        $params = $request->all();

        $validator = Validator::make(
            $params,
            ReturnCabinetValidates::getRule(),
            ReturnCabinetValidates::getMessages(),
            ReturnCabinetValidates::getAttributes()

        );

        if ($validator->fails()) {
            return AjaxResponse::isFailure($validator->errors()->first());
        }

        $res = ReturnCabinet::emil($params);
        if ($res) {
            return AjaxResponse::isSuccess(__('auth.SendSuccessfully'));
        } else {
            return AjaxResponse::isFailure(__('auth.Failsend'));
        }
    }

    /**
     * 还柜单导出页面
     * @author zt12700
     * @param Request $request
     */
    public function export()
    {
        return view('returnCabinet.exports');
    }

    /**
     * 还柜单导出
     * @author zt12700
     * @param Request $request
     */
    public function down(Request $request)
    {

        $params = $request->all();

        $validator = Validator::make(
            $params,
            ReturnCabinetValidates::downRule(),
            ReturnCabinetValidates::getMessages(),
            ReturnCabinetValidates::getAttributes()

        );

        if ($validator->fails()) {
            return AjaxResponse::isFailure($validator->errors()->first());
        }

        $res = ReturnCabinet::down($params);
        if ($res) {
            return AjaxResponse::isSuccess(__('auth.ExportTaskAddedSuccessfully'));
        } else {
            return AjaxResponse::isFailure(__('auth.ExportTaskAdditionFailed'));
        }
    }
}