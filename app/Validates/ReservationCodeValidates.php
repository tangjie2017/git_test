<?php
/**
 * @author zt12700
 * CreateTime: 2019/5/6 16:30
 *
 */

namespace App\Validates;


class ReservationCodeValidates
{
    /**
     * 修改字段时的验证
     * @author zt12700
     * @return array
     */
    public static function getRulesAdd()
    {
        return [
            'appointment_delivery_time' => 'required|date|after:now',
//            'contact_name' => 'required|max:30|regex:/^[a-zA-Z0-9_-\s]*$/',
            'contact_name' => ['required','max:30','regex:/^[\x{4e00}-\x{9fa5}\sA-Za-z0-9_-]+$/u'],
            'telephone' => ['required','max:30','regex:/^[0-9+\s]+$/'],
            'email' => 'required|max:40|regex:/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/',

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
            'max' => ':attribute '.__('auth.MaximumLength').':max',
            'regex' => ":attribute ".__('auth.IncorrectFormat'),
            'string' => ":attribute ".__('auth.IncorrectFormat'),
            'after' => ":attribute ".__('auth.GreaterThan'),
        ];
    }

    /**
     * 属性名称
     * @author zt12700
     */
    public static function getAttributes()
    {
        return [
            'appointment_delivery_time' => __('auth.AppointmentDeliveryTime'),
            'contact_name' => __('auth.ContactName'),
            'telephone' => __('auth.phone'),
            'email' => __('auth.email'),
        ];
    }

}