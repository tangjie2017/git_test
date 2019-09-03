<?php

namespace App\Auth\Services;

use App\Auth\Models\UserRole;

class UserRoleService
{
    /**
     * @param $userId
     * @return UserRole
     */
    public static function getByUserId($userId){
       return UserRole::where("UserId","=",$userId)->first();
    }
}