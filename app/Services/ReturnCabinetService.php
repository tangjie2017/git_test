<?php

namespace App\Services;

use App\Models\StaticState;
use App\Models\ReturnCabinet;

class ReturnCabinetService
{
    /**
     * 获取系统
     * @author zt12700
     * @return array
     */
    public static function getSystem($key = null)
    {
        $data = [
//            StaticState::SYSTEM_BIS => 'BIS',
            StaticState::SYSTEM_GC_OMS => 'GC-OMS',
//            StaticState::SYSTEM_EL_OMS => 'EL-OMS',
//            StaticState::SYSTEM_AE_OMS => 'AE-OMS',
        ];

        if ($key) {
            return $data[$key];
        }

        return $data;

    }

    /**
     * 目前只对接这两个仓库
     * @author zt12700
     * @param null $key
     * @return array|mixed
     */
    public static function getWarehouse($key = null)
    {
        $data = [
            StaticState::WAREHOUSE_USWE => __("auth.USWestWarehouse"),
            StaticState::WAREHOUSE_USEA => __("auth.USEastWarehouse")
        ];

        if ($key) {
            if(isset($data[$key])){
                return $data[$key];
            }

        }

        return $data;

    }

    /**
     * 获取柜型
     * @author zt12700
     * @return array
     */
    public static function getCabinetType()
    {
        return [
            StaticState::CABINET_TYPE_20GP => '20GP',
            StaticState::CABINET_TYPE_40GP => '40GP',
            StaticState::CABINET_TYPE_40HQ => '40HQ',
            StaticState::CABINET_TYPE_45HQ => '45HQ',
        ];
    }

    /**
     * 获取来源
     * @author zt12700
     * @return array
     */
    public static function getSource($key = null)
    {
        $data = [
            StaticState::SOURCE_CLIENT => __('auth.client'),
            StaticState::SOURCE_WAREHOUSE => __('auth.warehouse')
        ];

        if ($key) {
            if(isset($data[$key])){
                return $data[$key];
            }

        }

        return $data;
    }

    /**
     * 获取预约状态
     * @author zt12700
     * @return array
     */
    public static function getReservationStatus()
    {
        return [
            StaticState::RESERVATION_STATUS_NOT_EFFECTIVE => __('auth.NotActive'),
            StaticState::RESERVATION_STATUS_EFFECTIVE => __('auth.Effective'),
            StaticState::RESERVATION_STATUS_EXPIRED => __('auth.expired'),
            StaticState::RESERVATION_STATUS_END => __('auth.end'),
        ];
    }

    /**
     * 获取预约主状态
     * @author zt7239
     * @return array
     */
    public static function getStatus($key = null)
    {
        $data = [
            StaticState::RETURN_STATUS_UNLOADING => __('auth.UnloadingCabinet'),
            StaticState::RETURN_STATUS_ALREADY => __('auth.UnloadedCabinet'),
            StaticState::RETURN_STATUS_RETURN_UNLOADING => __('auth.Cabinets'),
            StaticState::RETURN_STATUS_RETURN_END => __('auth.ReturnedCabinet'),
        ];

        if ($key) {
            if(isset($data[$key])){
                return $data[$key];
            }

        }

        return $data;
    }

    /**
     * 获取货柜类型
     * @author zt12700
     * @return array
     */
    public static function getContainerType()
    {
        return [
            StaticState::CONTAINER_TYPE_ORDINARY => __('auth.ordinary'),
            StaticState::CONTAINER_TYPE_CABINET => __('auth.Cabinet'),
            StaticState::CONTAINER_TYPE_TO_FBA => __('auth.TransferToFBA'),
            StaticState::CONTAINER_TYPE_PART_TO_FBA => __('auth.PartiallyTransferredToFBA')
        ];
    }

    /**
     * 更新还柜单信息
     * @author zt7242
     * @date 2019/5/8 14:38
     * @param $cabinet_id
     * @param $actual_start_time
     * @param $actual_end_time
     * @return mixed
     */
    public static function updateReturnCabinetInfo($cabinet_id, $actual_start_time, $actual_end_time)
    {
        return ReturnCabinet::updateReturnCabinetInfo($cabinet_id, $actual_start_time, $actual_end_time);
    }


    /**
     * 更新还柜信息及插入还柜附件
     * @author zt7242
     * @date 2019/5/14 18:48
     * @param $files
     * @param $cabinet_id
     * @param $actual_return_time
     * @return bool
     */
    public static function updateAndInsertCabinetInfo($files,$cabinet_id,$actual_return_time)
    {
        return ReturnCabinet::updateAndInsertCabinetInfo($files,$cabinet_id,$actual_return_time);
    }
    /**
     * 通过预约单号获取还柜信息
     * @author zt7242
     * @date 2019/5/7 18:13
     * @param $reservation_id
     * @return mixed
     */
    public static function getCabinetInfoByReservationId($reservation_id)
    {
        return ReturnCabinet::getCabinetInfoByReservationId($reservation_id);
    }
    /**
     * 获取数据列表
     * @author zt12700
     */
    public static function getList($data,$limit)
    {
        return ReturnCabinet::getList($data,$limit);
    }

    /**
     * 导出还柜单信息
     * @author zt7242
     * @date 2019/4/25 13:06
     * @param $condition
     * @return mixed
     */
    public static function exportReturnCabinetInfoByCondition($condition)
    {
        return ReturnCabinet::exportReturnCabinetInfoByCondition($condition);
    }



    /**
     * 查询还柜id对应的仓库
     * @author zt7239
     * @param $cabinet_id
     * @return bool
     */
    public static function warehouseExistByCabinetId($cabinet_id)
    {
        return ReturnCabinet::warehouseExistByCabinetId($cabinet_id);
    }

}