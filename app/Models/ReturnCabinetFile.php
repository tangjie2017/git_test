<?php
/**
 * @author zt12700
 * CreateTime: 2019/4/26 14:00
 *
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ReturnCabinetFile extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'return_cabinet_file';

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