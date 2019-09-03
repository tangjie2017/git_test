<?php

namespace App\Auth\Services;

use App\Auth\Common\Config;
use App\Auth\Common\CurrentUser;
use App\Auth\Common\Enums\AccountType;
use App\Auth\Common\Response;
use App\Auth\Models\Permission;

/** 权限服务
 * Class PermissionService
 * @package App\Auth\Services
 */
class PermissionService
{
    /** 获取所有权限
     * @return mixed
     */
    public static function getAll(){
        return Permission::orderBy('Id')->orderBy('PermissionSort')->get();
    }

    /** 根据权限id获取权限
     * @param $id
     * @return mixed
     */
    public static function getById($id){
        return Permission::find($id);
    }

    /** 根据用户id获取权限
     * @param $userId
     * @return array
     */
    public static function getArrayByUserId($userId){
        $rolePermission = Permission::join('RolePermission', function ($join){
             $join->on('Permission.Id','=','RolePermission.PermissionId');
         })->join("UserRole",function($join){
             $join->on('RolePermission.RoleId','=','UserRole.RoleId');
        })->where('UserRole.UserId','=',$userId)->orderBy('PermissionSort')->select("Permission.*")->get();

        $userPermission = Permission::join('UserPermission', function ($join){
            $join->on('Permission.Id','=','UserPermission.PermissionId');
        })->where('UserPermission.UserId','=',$userId)->orderBy('PermissionSort')->select("Permission.*")->get();

        return collect($rolePermission->toArray())->merge($userPermission->toArray())->unique('Id')->sortBy("Id")->sortBy("PermissionSort")->values()->all();
    }

    /** 创建或修改
     * @param $permission
     * @return Response
     */
    public static function createOrUpdate($permission){
        if(empty($permission["Id"]) == false){
            $dbPermission = Permission::find($permission["Id"]);
            if(empty($dbPermission)){
                return Response::isFailure("权限不存在");
            }

            $dbPermission->Icon = $permission["Icon"];
            $dbPermission->Name = $permission["Name"];
            $dbPermission->ResourceName = $permission["ResourceName"];
            $dbPermission->PermissionSort = (int)$permission["PermissionSort"];
            $dbPermission->PermissionType = (int)$permission["PermissionType"];
            $dbPermission->Url = $permission["Url"];
            $dbPermission->save();
        }else{
            $dbPermission = new Permission();
            $dbPermission->ParentId = (int)$permission["ParentId"];
            $dbPermission->Icon = $permission["Icon"];
            $dbPermission->Name = $permission["Name"];
            $dbPermission->ResourceName = $permission["ResourceName"];
            $dbPermission->PermissionSort = (int)$permission["PermissionSort"];
            $dbPermission->PermissionType = (int)$permission["PermissionType"];
            $dbPermission->Url = $permission["Url"];
            $dbPermission->save();
        }

        return Response::isSuccess();
    }

    public static function del($id){
        Permission::find($id)->delete();
    }
}