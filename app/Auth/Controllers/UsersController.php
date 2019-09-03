<?php

namespace App\Auth\Controllers;

use App\Auth\Common\AjaxResponse;
use App\Auth\Common\Config;
use App\Auth\Common\CurrentUser;
use App\Auth\Common\Enums\AccountType;
use App\Auth\Common\StringExtension;
use App\Auth\Services\PermissionService;
use App\Auth\Services\RolePermissionService;
use App\Auth\Services\RoleService;
use App\Auth\Services\UserPermissionService;
use App\Auth\Services\UserRoleService;
use App\Auth\Services\UsersService;
use App\Auth\Validates\UsersValidate;
use Illuminate\Http\Request;
use Validator;


/** 用户控制器
 * Class UsersController
 * @package App\Auth\Controllers
 */
class UsersController extends BaseAuthController
{
    /** 用户首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){
        if(Config::disabledIframe() == false && $request->isBack == false){
            return view("iframe"
                ,[
                    "url"=>"/auth/users"
                ]);
        }

        return view("Users.index"
            ,[
                "isAdminSystem"=>Config::isAdminSystem()
            ]);
    }

    /** 查询
     * @param Request $request
     * @return mixed
     */
    public function query(Request $request){
        return UsersService::query(["param"=>$request->input("param")],$request->input("rows"))->toJson();
    }

    /** 创建或修改页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createOrUpdate(Request $request){
        $id = $request->input("id");
        $data = [];
        if($id>0){
            $user = UsersService::getById($id);
            if(empty($user) == false){
                $data = $user->toArray();
                if(Config::isAdminSystem()){
                    $userRole = UserRoleService::getByUserId($user->Id);
                    if(empty($userRole) == false){
                        $data['RoleId'] = $userRole->RoleId;
                    }
                }
            }
        }

        $roles = [];
        if(Config::isAdminSystem()){
            $roles = RoleService::getAll();
        }

        return view("Users.createOrUpdate",[
            "data"=>$data,
            "roles"=>$roles,
            "title"=>isset($data["Id"])&&$data["Id"]>0?__("auth.editUser"):__("auth.createUser"),
            "isAdminSystem"=>Config::isAdminSystem(),
            'validate'=>['rules'=>UsersValidate::getRules(),'messages'=>UsersValidate::getMessages(),'customAttributes'=>UsersValidate::getCustomAttributes()]
        ]);
    }

    /** 执行创建或修改
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doCreateOrUpdate(Request $request){
        if(empty($request->input("Id")) && empty($request->input("Password"))){
            return AjaxResponse::isFailure(__("auth.passwordRequired"));
        }

        $validator = Validator::make($request->all(),UsersValidate::getRules()
            ,UsersValidate::getMessages()
            ,UsersValidate::getCustomAttributes());
        if ($validator->fails()) {
            return AjaxResponse::isFailure($validator->errors()->first());
        }

        $requestData = StringExtension::trim($request->all());
        $result = UsersService::createOrUpdate($requestData,$request->input("RoleId"));
        if($result->status == 0){
            return AjaxResponse::isFailure($result->message);
        }

        return AjaxResponse::isSuccess();
    }

    /** 更新用户权限页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function updateUserPermission(Request $request){
        $userId = $request->input("id");
        $user = UsersService::getById($userId);
        if(empty($user)){
            return "用户不存在.";
        }

        $currentUser = CurrentUser::getCurrentUser();
        if(Config::isAdminSystem() == false){
            if($currentUser->accountType == AccountType::primary){
                if($currentUser->userId != $user->ParentUserId){
                    return "你没有权限执行此操作.";
                }
            }
            else{
                if($currentUser->primaryUserId != $user->ParentUserId){
                    return "你没有权限执行此操作.";
                }
            }
        }

        $permissions = PermissionService::getAll();
        $userPermissions = UserPermissionService::getArraryByUserId($userId);
        $ztreeArr = [];
        foreach ($permissions as $p) {
            $ztreeArr[] = array(
                'id' => $p['Id'],
                'pId' => $p['ParentId'],
                'name' => $p['Name'],
                'open' => true,
                'checked' => false,
            );
        }

        $rolePermissions = [];
        if(Config::isAdminSystem()){
            $rolePermissions = RolePermissionService::getArrayByUserId($userId);
        }

        foreach ($ztreeArr as $k => $v) {
            if(Config::isAdminSystem()){
                foreach ($rolePermissions as $rp) {
                    if($v['id'] == $rp['PermissionId']){
                        $v['checked'] = true;
                        $v['chkDisabled'] = true;
                        $ztreeArr[$k] = $v;
                        break;
                    }
                }
            }

            foreach ($userPermissions as $up) {
                if($v['id'] == $up['PermissionId']){
                    $v['checked'] = true;
                    $ztreeArr[$k] = $v;
                    break;
                }
            }
        }

        return view("Users.updateUserPermission",[
            'id' => $userId,
            'znodes' => json_encode($ztreeArr)
        ]);
    }

    /** 执行更新用户权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function doUpdateUserPermission(Request $request){
        $userId = $request->input("id");
        $user = UsersService::getById($userId);
        if(empty($user)){
            return "用户不存在.";
        }

        $currentUser = CurrentUser::getCurrentUser();
        if(Config::isAdminSystem() == false){
            if($currentUser->accountType == AccountType::primary){
                if($currentUser->userId != $user->ParentUserId){
                    return "你没有权限执行此操作";
                }
            }
            else{
                if($currentUser->primaryUserId != $user->ParentUserId){
                    return "你没有权限执行此操作";
                }
            }
        }

        UserPermissionService::updateUserPermission($userId,$request->input("permissionIds"));
        return AjaxResponse::isSuccess();
    }
}