<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ReservationManagementFile extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'reservation_management_file';

    /**
     * 与模型关联的数据表主键
     * 还柜单号id
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

}