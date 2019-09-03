<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/10
 * Time: 19:19
 */

namespace App\Auth\Validates;


class RoleValidate
{
    public static function getRules(){
        return  [
            'Name' => 'required|max:32',
        ];
    }

    public static function getMessages(){
        return [
        ];
    }

    public static function getCustomAttributes(){
        return [
            'Name' => __("auth.name")
        ];
    }
}