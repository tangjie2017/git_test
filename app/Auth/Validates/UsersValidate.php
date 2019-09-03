<?php
namespace App\Auth\Validates;

class UsersValidate
{
    public static function getRules(){
        return  [
            'UserCode' => 'required|between:6,20|regex:/^([a-zA-Z0-9_]+)$/',
            'UserName' => 'required|max:20',
            'Password' => 'nullable:between:8,20|regex:/^(?=.*[0-9].*)(?=.*[A-Z].*)(?=.*[a-z].*).{8,16}$/',
            'Email' => 'required|max:50|regex:/^[a-zA-Z0-9_\.-]+\@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4}$/',
            'PhoneNumber' => 'required|max:20|regex:/^1\d{10}$/',
            'TelPhone' => array('nullable','max:20','regex:/^(\(\d{3,4}\)|\d{3,4}-)?\d{7,8}$/')
        ];
    }

    public static function getMessages(){
        return [
            'UserCode.regex'=>__("auth.UserCodeRegex"),
            'Password.regex'=>__("auth.PasswordRegex"),
            'Email.regex'=>__("auth.EmailRegex"),
            'PhoneNumber.regex'=>__("auth.PhoneNumberRegex"),
            'TelPhone.regex'=>__("auth.TelPhoneRegex"),
        ];
    }

    public static function getCustomAttributes(){
        return [
            'UserCode' => __("auth.userCode"),
            'UserName' => __("auth.userName"),
            'Password' => __("auth.password"),
            'Email' => __("auth.email"),
            'PhoneNumber' => __("auth.phoneNumber"),
            'TelPhone' => __("auth.telPhone"),
            'Status' => __("auth.status"),
            'RoleId' => __("auth.role"),
        ];
    }
}