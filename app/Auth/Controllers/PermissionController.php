<?php

namespace App\Auth\Controllers;


use App\Auth\Common\AjaxResponse;
use App\Auth\Common\Config;
use App\Auth\Common\CurrentUser;
use App\Auth\Common\Enums\AccountType;
use App\Auth\Common\StringExtension;
use App\Auth\Services\PermissionService;
use App\Auth\Validates\PermissionValidate;
use Illuminate\Http\Request;
use Validator;

/** 权限控制器
 * Class PermissionController
 * @package App\Auth\Controllers
 */
class PermissionController extends BaseAuthController
{
    /** 权限首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function index(Request $request){
        if(Config::disabledIframe() == false && $request->isBack == false){
            return view("iframe"
                ,[
                    "url"=>"/auth/permission"
                ]);
        }

        $currentUser = CurrentUser::getCurrentUser();
        if(Config::isAdminSystem() == false && $currentUser->accountType != AccountType::admin){
            return "你没有权限执行该操作";
        }

        $permissions = PermissionService::getAll();
        $ztreeArr = [];
        foreach ($permissions as $p) {
            $isExistsChildren = false;
            foreach ($permissions as $v){
                if($v['ParentId'] == $p['Id']){
                    $isExistsChildren = true;
                    break;
                }
            }
            $ztreeArr[] = array(
                'id' => $p['Id'],
                'pId' => $p['ParentId'],
                'name' => $p['Name'],
                'open' => true,
                'iconSkin' =>  $p['PermissionType'] == 1 && $isExistsChildren == false ? 'icon01' : null,
            );
        }

        return view("Permission.index"
            ,[
                "znodes"=>json_encode($ztreeArr)
            ]);
    }

    /** 创建或修改页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function createOrUpdate(Request $request){
        $currentUser = CurrentUser::getCurrentUser();
        if(Config::isAdminSystem() == false && $currentUser->accountType != AccountType::admin){
            return "没有权限执行该操作.";
        }

        $data = [];
        $id = $request->input("id");
        if(empty($id) == false){
            $permission = PermissionService::getById($id);
            if(empty($permission) == false){
                $data = $permission->toArray();
            }
        }

        return view("Permission.createOrUpdate",[
            'data' => $data,
            'validate'=>[
                'rules'=>PermissionValidate::getRules(),
                'messages'=>PermissionValidate::getMessages()
                ,'customeAttributes'=>PermissionValidate::getCustomAttributes()
            ]
        ]);
    }

    /** 执行创建或修改
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function doCreateOrUpdate(Request $request){
        $currentUser = CurrentUser::getCurrentUser();
        if(Config::isAdminSystem() == false && $currentUser->accountType != AccountType::admin){
            return "没有权限执行此操作.";
        }

        $validator = Validator::make($request->all(),PermissionValidate::getRules()
            ,PermissionValidate::getMessages()
            ,PermissionValidate::getCustomAttributes());
        if ($validator->fails()) {
            return AjaxResponse::isFailure($validator->errors()->first());
        }

        $requestData = StringExtension::trim($request->all());
        $result = PermissionService::createOrUpdate($requestData);
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
        $currentUser = CurrentUser::getCurrentUser();
        if(Config::isAdminSystem() == false && $currentUser->accountType != AccountType::admin){
            return "没有权限执行此操作.";
        }

        if(empty($request->input("id")) == false){
            PermissionService::del($request->input("id"));
            return AjaxResponse::isSuccess();
        }

        return AjaxResponse::isFailure("请求参数无效.");
    }
}