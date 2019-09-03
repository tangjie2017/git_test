<?php
/**
 * @author zt12700
 * CreateTime: 2019/4/26 13:17
 *
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class InboundOrder extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'inbound_order';

    /**
     * 与模型关联的数据表主键
     * 还柜单号id
     * @var string
     */
    protected $primaryKey = 'inbound_order_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 入库单与预约单为多对一关系
     * @author zt7242
     * @date 2019/5/7 11:18
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ReservationManagement()
    {
        return $this->belongsTo('App\Models\ReservationManagement','reservation_number_id','reservation_number_id');
    }


    /**
     * 获取是否已创建的入库单
     * @author zt7239
     * @param $serData
     * @return bool
     */
    public function getInboundData($serData)
    {
        $inb  = self::query();
        $inb = $inb->where('inbound_order_number', $serData['inbound_order_number']);
        $inb = $inb->with('ReservationManagement');

        $inb = $inb->whereHas('ReservationManagement',function ($query) use ($serData){
            $query->whereNotIn('reservation_management.status',[6]);
        });
        $inb = $inb->first();

        if($inb){
            return $serData['inbound_order_number'];
        }else{
            return false;
        }
    }


    /**
     * 释放出入库单关联的预约单，我们的数据库里状态为废弃的入库单
     * @author zt7239
     * @param $res
     * @return mixed
     */
    public static function filterStatus($res)
    {

        $inb  = self::query();

        $data = [];
        foreach ($res as $v){
            $data[] = [
                'inbound_order_number' => $v['receiving_code']
            ];
        }

        $inb = $inb->whereIn('inbound_order_number', $data);
        $inb = $inb->with('ReservationManagement');

        $inb = $inb->whereHas('ReservationManagement',function ($query) use ($data){
            $query->whereNotIn('reservation_management.status',[6]);
        });
        $inb = $inb->get()->toArray();

        foreach ($res as $k=>$v){
            foreach ($inb as $value){
                if($v['receiving_code'] == $value['inbound_order_number']){
                    unset($res[$k]);
                }
            }
        }

        $res = array_values($res);
        return $res;
    }

    /**
     * 释放出从谷仓过来的入库单关联的预约单，我们的数据库里状态为废弃的入库单
     * @author zt7239
     * @param $inbound_order_number
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public static function filterInboundData($inbound_order_number)
    {
        $inb  = self::query();
        $inb = $inb->where('inbound_order_number', $inbound_order_number);
        $inb = $inb->with('ReservationManagement');

        $inb = $inb->whereHas('ReservationManagement',function ($query) use ($inbound_order_number){
            $query->whereNotIn('reservation_management.status',[6]);
        });
        $inb = $inb->first();

        return $inb;
    }


}