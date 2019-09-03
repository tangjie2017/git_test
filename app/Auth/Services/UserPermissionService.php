<?php

namespace App\Auth\Services;


use App\Auth\Models\UserPermission;

class UserPermissionService
{
    public static function getArraryByUserId($userId){
        return UserPermission::where("UserId","=",$userId)->get()->toArray();
    }

    public static function updateUserPermission($userId,$permissionIds){
        UserPermission::where("UserId","=",$userId)->delete();
        if(empty($permissionIds) == false){
            foreach ($permissionIds as $pId){
                UserPermission::create([
                    'PermissionId'=> $pId,
                    'UserId'=> $userId,
                ]);
            }
        }
    }
}