<?php

namespace App\Auth\Services;

use App\Auth\Common\Response;
use App\Auth\Models\Role;

class RoleService
{
    public static function getAll(){
        return Role::get()->toArray();
    }

    public static function query($condition,$rows){
        if(empty($condition['Name']) == false){
            return Role::where("Name","like","%".$condition["Name"]."%")->paginate($rows);
        }

        return Role::paginate($rows);
    }

    public static function getById($id){
        return Role::find($id);
    }

    public static function createOrUpdate($role){
        if(empty($role['Id']) == false){
            $dbRole = Role::find($role['Id']);
            if(empty($role)){
                return Response::isFailure("角色不存在.");
            }

            $dbRole->Name = $role['Name'];
            $dbRole->save();
        }else{
            $dbRole = new Role();
            $dbRole->Name = $role['Name'];
            $dbRole->save();
        }

        return Response::isSuccess();
    }

    public static function del($id){
        Role::find($id)->delete();
    }
}