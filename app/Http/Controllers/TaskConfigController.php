<?php

namespace App\Http\Controllers;

use App\Auth\Controllers\BaseAuthController;
use App\Models\ConsigneeNoticeList;
use App\Services\TaskConfigService;
use Illuminate\Http\Request;

class TaskConfigController extends BaseAuthController
{

    /**
     * 任务配置首页
     * @author zt7242
     * @date 2019/4/23 9:31
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $config = TaskConfigService::getConfigInfo();
        $cycle = TaskConfigService::getCycleInfo();
        $mesConsignee = ConsigneeNoticeList::getMesInfo();
        if(count($mesConsignee) == 1){
            $mesConsignee[] = [
                'consignee_name' => '',
                'consignee_telephone' => '',
            ];
        }
        $emailConsignee = ConsigneeNoticeList::getEmailInfo();
        if(count($emailConsignee) == 1){
            $emailConsignee[] = [
                'consignee_name' => '',
                'consignee_email' => '',
            ];
        }
        return view('taskCenter.taskConfig.index',[
            'config' => $config,
            'cycle' => $cycle,
            'mesConsignee' => $mesConsignee,
            'emailConsignee' => $emailConsignee
        ]);
    }

    /**
     * 任务配置-通知保存配置
     * @author zt7242
     * @date 2019/4/26 19:36
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $info = $request->all();

        //验证数据并插入数据
        return TaskConfigService::insertAndValidateConfig($info);

    }

    /**
     * 任务配置-删除周期保存
     * @author zt7242
     * @date 2019/4/28 11:44
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function cleanupCycleStore(Request $request)
    {
        $data = $request->all();
        return TaskConfigService::saveCycleConfig($data);
    }

}
