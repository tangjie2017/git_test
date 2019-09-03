<?php

namespace App\Http\Controllers;

use App\Auth\Common\AjaxResponse;
use App\Auth\Controllers\BaseAuthController;
use App\Services\RouteService;
use Illuminate\Http\Request;
use Validator;
use App\Validates\MenuValidates;
class MenuManagementController extends BaseAuthController
{

    /**
     * 菜单首页
     * @author zt7242
     * @date 2019/5/5 9:55
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //获取目录数据
        $list = RouteService::getMenuData();
        $tree = json_encode($list,true);
        return view('menuManagement.index',['tree' => $tree,'node' => $list]);
    }

    /**
     * 菜单管理编辑页面
     * @author zt7242
     * @date 2019/5/6 9:43
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $list = RouteService::getMenuData();
        $route = RouteService::getMenuInfoById($id);
        return view('menuManagement.edit',['route' => $route,'node' => $list]);
    }

    /**
     * 菜单管理保存
     * @author zt7242
     * @date 2019/5/6 9:43
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMenu(Request $request)
    {
        $requestData = $request->all();
        $permissionId= $request->input('route_id');
        $validator = Validator::make(
            $requestData,
            $permissionId ? MenuValidates::getRulesUpdate() : MenuValidates::getRulesCreate(),
            MenuValidates::getMessages(),
            MenuValidates::getAttributes()
        );
        if ($validator->fails()) {
            return AjaxResponse::isFailure($validator->errors()->first());
        }


        $bool = RouteService::saveData($requestData);

        if ($bool) {
            if($bool['status'] == 2){
                return AjaxResponse::isFailure(__('auth.NoDataObtained'));
            }
            return AjaxResponse::isSuccess(__('auth.saveSuccess'));
        } else {
            return AjaxResponse::isFailure(__('auth.saveFailure'));
        }

    }


    /**
     * 删除菜单
     * @author zt7242
     * @date 2019/5/6 11:17
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delMenu(Request $request)
    {
        $strIds = $request->input('ids');
        $res = RouteService::delMenuInfo($strIds);
        if($res){
            return AjaxResponse::isSuccess(__('auth.deleteSuccess'));
        }else{
            return AjaxResponse::isSuccess(__('auth.deleteFail'));
        }
    }

}
