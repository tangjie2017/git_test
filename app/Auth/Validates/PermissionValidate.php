<?php

namespace App\Auth\Validates;


class PermissionValidate
{
    public static function getRules(){
        return  [
            'Name' => 'required|max:100',
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