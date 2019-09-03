<?php
/**
 * @author zt12700
 * CreateTime: 2019/4/26 16:56
 *
 */

namespace App\Validates;


class ReturnCabinetValidates
{

    /**
     * 修改邮箱验证
     * @author zt12700
     * @return array
     */
    public static function getRule()
    {
        //self::$free = offsetSet('curr_date',date('Y-m-d H-i-s'));
        return [
            'email' => 'required|max:40|regex:/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/',
            'notice_return_time' => 'required|date|after:now',

        ];
    }

    public static function downRule()
    {
        return [
            'exports_name' => 'required|max:50|string'
        ];
    }

    /**
     * 提示错误信息
     * @author zt12700
     * @return array
     */
    public static function getMessages()
    {
        return [
            'required' => ':attribute '.__('auth.canNotBeEmpty'),
            'max' => ':attribute '.__('auth.MaximumLength').' :max ',
            'regex' => ":attribute ".__('auth.IncorrectFormat'),
            'after' => ":attribute".__('auth.GreaterThan'),
            'string' => ":attribute ".__('auth.IncorrectFormat'),
        ];
    }

    /**
     * 属性名称
     * @author zt7239
     * @return array
     */
    public static function getAttributes()
    {
        return [
            'notice_return_time' => __('auth.NotifyReturnTime'),
            'email' => __('auth.email'),
            'exports_name' => __('auth.name')
        ];
    }

}