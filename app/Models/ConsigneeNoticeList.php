<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Self_;

/**
 * 路由模型
 */
class ConsigneeNoticeList extends Model
{

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'consignee_notice_list';

    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'consignee_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * 获取短信收件人信息
     * @author zt7242
     * @date 2019/4/29 16:09
     * @return mixed
     */
    public static function getMesInfo()
    {
        return self::where('origin',1)->get()->toArray();

    }

    /**
     * 获取邮件收件人信息
     * @author zt7242
     * @date 2019/4/29 16:08
     * @return mixed
     */
    public static function getEmailInfo()
    {
        return self::where('origin',2)->get()->toArray();
    }

    /**
     * 获取收件人邮箱
     * @author zt7242
     * @date 2019/4/29 16:07
     * @return mixed
     */
    public static function getEmail()
    {
        return self::where('origin',2)->pluck('consignee_email')->toArray();
    }

}
