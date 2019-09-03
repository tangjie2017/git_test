<?php

namespace App\Auth\Services;


use App\Auth\Models\RolePermission;

class RolePermissionService
{
    public static function getArrayByUserId($userId){
        return RolePermission::join("UserRole",function($join){
            $join->on("UserRole.RoleId","=","RolePermission.RoleId");
        })->where("UserRole.UserId","=",$userId)->select("RolePermission.*")->get()->toArray();
    }

    public static function getArrayByRoleId($roleId){
        return RolePermission::where("RoleId","=",$roleId)->get()->toArray();
    }

    public static function updateRolePermission($roleId,$permissionIds){
        RolePermission::where("RoleId","=",$roleId)->delete();
        if(empty($permissionIds) == false){
            foreach ($permissionIds as $pId){
                RolePermission::create([
                    'PermissionId'=> $pId,
                    'RoleId'=> $roleId,
                ]);
            }
        }
    }
}