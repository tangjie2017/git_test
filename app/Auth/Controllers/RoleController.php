<?php

namespace App\Auth\Controllers;


use App\Auth\Common\AjaxResponse;
use App\Auth\Common\Config;
use App\Auth\Common\StringExtension;
use App\Auth\Services\PermissionService;
use App\Auth\Services\RolePermissionService;
use App\Auth\Services\RoleService;
use App\Auth\Validates\RoleValidate;
use Illuminate\Http\Request;
use Validator;

/** 角色控制器
 * Class RoleController
 * @package App\Auth\Controllers
 */
class RoleController extends BaseAuthController
{
    /** 角色首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function index(Request $request){
        if(Config::disabledIframe() == false && $request->isBack == false){
            return view("iframe"
                ,[
                    "url"=>"/auth/role"
                ]);
        }

        if(Config::isAdminSystem() == false){
            return "没有权限执行此操作.";
        }
        return view("Role.index");
    }

    /** 查询
     * @param Request $request
     * @return string
     */
    public function query(Request $request){
        if(Config::isAdminSystem() == false){
            return "没有权限执行此操作.";
        }

        return RoleService::query(["Name"=>$request->input("Name")],$request->input("rows"))->toJson();
    }

    /** 创建或修改页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function createOrUpdate(Request $request){
        if(Config::isAdminSystem() == false){
            return "没有权限执行此操作.";
        }

        $data = [];
        if(empty($request->input("id")) == false){
            $role = RoleService::getById((int)$request->input("id"));
            if(empty($role) == false){
                $data = $role->toArray();
            }
        }

        return view("Role.createOrUpdate",[
            'data'=>$data,
            "title"=>isset($data["Id"])&&$data["Id"]>0?"编辑角色":"创建角色",
            'validate'=>[
                'rules'=>RoleValidate::getRules(),
                'messages'=>RoleValidate::getMessages()
                ,'customeAttributes'=>RoleValidate::getCustomAttributes()
            ]
        ]);
    }

    /** 执行创建或修改
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function doCreateOrUpdate(Request $request){
        if(Config::isAdminSystem() == false){
            return "没有权限执行此操作.";
        }

        $validator = Validator::make($request->all(),RoleValidate::getRules()
            ,RoleValidate::getMessages()
            ,RoleValidate::getCustomAttributes());
        if ($validator->fails()) {
            return AjaxResponse::isFailure($validator->errors()->first());
        }

        $requestData = StringExtension::trim($request->all());
        $result = RoleService::createOrUpdate($requestData);
        if($result->status == 0){
            return AjaxResponse::isFailure($result->message);
        }

        return AjaxResponse::isSuccess();
    }

    /** 执行删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function doDelete(Request $request){
        if(Config::isAdminSystem() == false){
            return "没有权限执行此操作.";
        }

        if(empty($request->input("id")) == false){
            RoleService::del($request->input("id"));
            return AjaxResponse::isSuccess();
        }

        return AjaxResponse::isFailure("请求参数无效.");
    }

    /** 更新角色权限页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function updateRolePermission(Request $request){
        if(Config::isAdminSystem() == false){
            return "没有权限执行此操作.";
        }

        $roleId = $request['id'];
        $permissions = PermissionService::getAll();
        $ztreeArr = [];
        $rolePermissions =  RolePermissionService::getArrayByRoleId($roleId);

        foreach ($permissions as $k => $p) {
            $ztreeArr[$k] = array(
                'id' => $p['Id'],
                'pId' => $p['ParentId'],
                'name' => $p['Name'],
                'open' => true,
                'checked' => false,
            );

            foreach ($rolePermissions as $rp) {
                if($p['Id'] == $rp['PermissionId']){
                    $ztreeArr[$k]['checked'] = true;
                    break;
                }
            }
        }

        return view("Role.updateRolePermission",[
            'id' => $roleId,
            'znodes' => json_encode($ztreeArr)
        ]);
    }

    /** 执行更新角色权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function doUpdateRolePermission(Request $request){
        if(Config::isAdminSystem() == false){
            return "没有权限执行此操作.";
        }

        $roleId = $request['id'];
        RolePermissionService::updateRolePermission($roleId,$request->input("permissionIds"));
        return AjaxResponse::isSuccess();
    }
}