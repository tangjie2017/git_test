<?php

namespace App\Services;
use App\Models\User;
use App\Models\Warehouse;

class WarehouseService
{
    /**
     * @description 根据仓库代码获取时区
     * @author zt7242
     * @date 2019/4/11 11:20
     * @param $warehouse_code
     * @return mixed
     */
    public static function getTimeZoneByCode($warehouse_code)
    {
        return Warehouse::getTimeZoneByCode($warehouse_code);
    }

    /**
     * 获取所有仓库信息
     * @author zt6535
     * CreateTime: 2019/3/12 13:50
     * @return \App\Models\Role[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getWarehouses()
    {
        return Warehouse::getWarehouses();
    }

    /**
     * 通过id获取仓库信息
     * @author zt7239
     * @param $warehouse_id
     * @return mixed
     */
    public static function getWarehousesById($warehouse_id)
    {
        return Warehouse::getWarehousesById($warehouse_id);
    }

    /**
     * 根据条件仓库信息
     * @author zt6768
     * @param array $conditon 条件
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getInfoByConditon($conditon)
    {
        return Warehouse::getInfoByConditon($conditon);
    }

}