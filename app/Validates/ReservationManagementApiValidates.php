<?php
namespace App\Validates;

/**
 * 谷仓预约单创建接口验证
 * @author zt7239
 * Class ReservationManagementValidates
 * @package App\Validates
 */
class ReservationManagementApiValidates
{
    /**
     * 创建时的字段验证
     * @author zt7239
     * @return array
     */
    public static function getRulesCreate()
    {
        return [
            'warehouse_code' => 'required|max:30',
            'warehouse_name' => 'required|max:30',
            'customer_code' => 'required|max:50',
            'type' => 'required|integer',
            'cabinet_type' => 'required|integer',
            'container_type' => 'required|integer',
            'customs_clearance_time' => ['required','date_format:Y-m-d H:i:s'],
            'arrival_time' => ['required','date_format:Y-m-d H:i:s'],
            'earliest_delivery_time' => ['required','date_format:Y-m-d H:i:s'],
            'latest_delivery_time' => ['required','date_format:Y-m-d H:i:s'],
            'contact_name' => ['required','max:30','regex:/^[\x{4e00}-\x{9fa5}\sA-Za-z0-9_-]+$/u'],
            'email' => 'required|max:40|string|email',
            'telephone' => ['required','max:30','regex:/^[0-9+\s]+$/'],
            'operator' => 'required|max:30',
            'operating_time' => ['required','date_format:Y-m-d H:i:s'],
        ];
    }

    public static function getInboundCreate()
    {
        return [
            'inbound_order_number' => 'required|max:40',
//            'tracking_number' => 'required|max:30',
            'customer_code' => 'required|max:30',
            'warehouse_code' => 'required|max:30',
            'warehouse_name' => 'required|max:30',
            'products_number' => ['required','integer'],
            'box_number' => ['required','integer'],
            'sku_species_number' => ['required','integer'],
            'weight' => ['required','regex:/^[0-9]+(.[0-9]{1,5})?$/'],
            'volume' => ['required','regex:/^[0-9]+(.[0-9]{1,5})?$/'],
            'created_at' => ['required','date_format:Y-m-d H:i:s'],
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
            'reservation_number' => 'required|max:50',
            'warehouse_code' => 'required|max:30',
            'warehouse_name' => 'required|max:30',
            'customer_code' => 'required|max:30',
            'type' => 'required|integer',
            'cabinet_type' => 'required|integer',
            'container_type' => 'required|integer',
            'customs_clearance_time' => ['required','date_format:Y-m-d H:i:s'],
            'arrival_time' => ['required','date_format:Y-m-d H:i:s'],
            'earliest_delivery_time' => ['required','date_format:Y-m-d H:i:s'],
            'latest_delivery_time' => ['required','date_format:Y-m-d H:i:s'],
            'contact_name' => ['required','max:30','regex:/^[\x{4e00}-\x{9fa5}\sA-Za-z0-9_-]+$/u'],
            'email' => 'required|max:40|string|email',
            'telephone' => ['required','max:30','regex:/^[0-9+\s]+$/'],
            'operator' => 'required|max:30',
            'operating_time' => ['required','date_format:Y-m-d H:i:s'],
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
            'integer' => ':attribute '.__('auth.MustBeAnInteger'),
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
            'reservation_number' => __('auth.ReservationNumber'),
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
            'exports_name' => __('auth.ExportedTaskName'),
            'operator' => __('auth.Operator'),
            'operating_time' => __('auth.OperatingTime'),
            'inbound_order_number' => __('auth.InboundOrderNumber'),
            'sea_cabinet_number' => __('auth.SeaCabinetNumber'),
            'customer_code' => __('auth.CustomerCode'),
            'warehouse_name' => __('auth.warehouseName'),
            'products_number' => __('auth.NumberOfProducts'),
            'box_number' => __('auth.NumberOfBoxes'),
            'sku_species_number' => __('auth.skuSpeciesNumber'),
            'weight' => __('auth.weight'),
            'volume' => __('auth.volume'),
            'created_at' => __('auth.CreationTime'),
        ];
    }







}