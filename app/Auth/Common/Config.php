<?php

namespace App\Auth\Common;


/** 配置
 * Class Config
 * @package App\Auth\Common
 */
class Config
{
    /**
     * 是否是后台系统（默认false）
     * @return bool
     */
    public static function isAdminSystem(){
        $authConfig = config('app.auth');
        return boolval($authConfig['IsAdminSystem']);
    }

    /**
     * Auth模块页面是否禁用iframe（默认false。如果你的系统内部使用了iframe，建议设置为true）
     * @return bool
     */
    public static function disabledIframe(){
        $authConfig = config('app.auth');
        return boolval($authConfig['DisabledIframe']);
    }

    /**
     * 登录是否需要验证码（默认为true）
     * @return bool
     */
    public static function requiredVerifyCode(){
        $authConfig = config('app.auth');
        if(isset($authConfig['RequiredVerifyCode']) == false){
            return true;
        }

        return boolval($authConfig['RequiredVerifyCode']);
    }

    /**
     * 设置登录错误次数超过后，使用验证码（默认：错误3次后使用验证码。如果设置为0，表示一直需要验证码。设置Auth.RequiredVerifyCode为true时有效"）
     * @return int
     */
    public static function loginErrorNumberEnableVerifyCode(){
        $authConfig = config('app.auth');
        if(isset($authConfig['LoginErrorNumberEnableVerifyCode']) == false){
            return 3;
        }

        return intval($authConfig['LoginErrorNumberEnableVerifyCode']);
    }

    /**
     * 限制登录错误次数超过多少次后，锁定账号不允许登录（默认：10次，与Auth.LimitLoginErrotTime配合使用。如果设置为0，不进行用户锁定）
     * @return int
     */
    public static function limitLoginErrorNumber(){
        $authConfig = config('app.auth');
        if(isset($authConfig['LimitLoginErrorNumber']) == false){
            return 10;
        }

        return intval($authConfig['LimitLoginErrorNumber']);
    }

    /**
     * 限制登录错误超过指定错误后，多长时间内锁定账号不允许登录（单位：分钟，默认：30分钟。如果设置为0，不进行用户锁定）
     * @return int
     */
    public static function limitLoginErrorTime(){
        $authConfig = config('app.auth');
        if(isset($authConfig['LimitLoginErrorTime']) == false){
            return 30;
        }

        return intval($authConfig['LimitLoginErrorTime']);
    }
}