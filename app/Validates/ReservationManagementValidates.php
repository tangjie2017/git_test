<?php
namespace App\Validates;

/**
 * 预约单创建验证
 * @author zt7239
 * Class ReservationManagementValidates
 * @package App\Validates
 */
class ReservationManagementValidates
{
    /**
     * 创建时的字段验证
     * @author zt7239
     * @return array
     */
    public static function getRulesCreate()
    {
        return [
            'system' => 'required',
            'warehouse_code' => 'required',
            'type' => 'required',
            'cabinet_type' => 'required',
            'container_type' => 'required',
            'customs_clearance_time' => ['required','date_format:Y-m-d H:i:s'],
            'arrival_time' => ['required','date_format:Y-m-d H:i:s'],
            'earliest_delivery_time' => ['required','date_format:Y-m-d H:i:s'],
            'latest_delivery_time' => ['required','date_format:Y-m-d H:i:s'],
            'contact_name' => ['required','max:30','regex:/^[\x{4e00}-\x{9fa5}\sA-Za-z0-9_-]+$/u'],
            'email' => 'required|max:40|string|email',
            'telephone' =>['required','max:30','regex:/^[0-9+\s]+$/'],

        ];
    }

    /**
     * 编辑时的验证
     * @author zt7239
     * @return array
     */
    public static function getRulesUpdate()
    {
        return [
            'system' => 'required',
            'warehouse_code' => 'required',
            'type' => 'required',
            'cabinet_type' => 'required',
            'container_type' => 'required',
            'customs_clearance_time' => ['required','date_format:Y-m-d H:i:s'],
            'arrival_time' => ['required','date_format:Y-m-d H:i:s'],
            'earliest_delivery_time' => ['required','date_format:Y-m-d H:i:s'],
            'latest_delivery_time' => ['required','date_format:Y-m-d H:i:s'],
            'contact_name' => ['required','max:30','regex:/^[\x{4e00}-\x{9fa5}\sA-Za-z0-9_-]+$/u'],
            'email' => 'required|max:40|string|email',
            'telephone' =>['required','max:30','regex:/^[0-9+\s]+$/'],
        ];
    }

    /**
     * 提示错误信息
     * @author zt7239
     * @return array
     */
    public static function getMessages()
    {
        return [
            'required' => ':attribute '.__('auth.canNotBeEmpty'),
            'max' => ':attribute '.__('auth.MaximumLength').' :max ',
            'regex' => ":attribute ".__('auth.IncorrectFormat'),
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
            'system' => __('auth.system'),
            'warehouse_code' => __('auth.DestinationWarehouse'),
            'type' => __('auth.type'),
            'cabinet_type' => __('auth.CabinetType'),
            'container_type' => __('auth.ContainerType'),
            'customs_clearance_time' => __('auth.CustomsClearanceTime'),
            'arrival_time' => __('auth.arrivalTime'),
            'earliest_delivery_time' => __('auth.EarliestTime'),
            'latest_delivery_time' => __('auth.LatestDeliveryTime'),
            'contact_name' => __('auth.ContactName'),
            'telephone' => __('auth.phone'),
            'email' => __('auth.email'),
            'exports_name' => __('auth.ExportedTaskName')
        ];
    }

    public static function downRule()
    {
        return [
            'exports_name' => 'required|max:50|string'
        ];
    }






}