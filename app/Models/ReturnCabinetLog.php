<?php
/**
 * @author zt12700
 * CreateTime: 2019/4/26 15:27
 *
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ReturnCabinetLog extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'return_cabinet_log';

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