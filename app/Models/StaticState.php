<?php

namespace App\Models;

class StaticState
{

    /**系统**/
    const SYSTEM_BIS = 1;           //BIS系统      预约系统
    const SYSTEM_GC_OMS = 2;        //GC-OMS系统    目前只对接oms系统
//    const SYSTEM_EL_OMS = 3;        //EL-OMS系统
//    const SYSTEM_AE_OMS = 4;        //AE-OMS系统
    /**系统**/

    const WAREHOUSE_USWE = 'USWE';  //美西仓库
    const WAREHOUSE_USEA = 'USEA';  //美东仓库

    /**来源**/
    const SOURCE_CLIENT = 1;        //客户   其他系统创建就是客户创建的
    const SOURCE_WAREHOUSE = 2;     //仓库   在rms后台创建的就是仓库创建的
    /**来源**/

    /**预约类型**/
    const TYPE_TIME_CABINET = 1;        //限时柜
    const TYPE_NON_TIME_CABINET = 2;     //非限时柜
    /**预约类型**/

    /**柜型**/
    const CABINET_TYPE_20GP = 1;      //20GP
    const CABINET_TYPE_40GP = 2;     //40GP
    const CABINET_TYPE_40HQ = 3;     //40HQ
    const CABINET_TYPE_45HQ = 4;     //45HQ
    /**柜型**/

    /**货柜类型**/
    const CONTAINER_TYPE_ORDINARY = 1;          //普通
    const CONTAINER_TYPE_CABINET = 2;           //拼柜
    const CONTAINER_TYPE_TO_FBA = 3;            //转FBA
    const CONTAINER_TYPE_PART_TO_FBA = 4;       //部分转FBA
    /**货柜类型**/

    /**预约单的主状态**/
    const STATUS_DRAFT = 1;                     //草稿
    const STATUS_WAIT_RESERVATION = 2;          //待预约
    const STATUS_WAIT_APPROVAL = 3;             //待审批
    const STATUS_WAIT_SEND_WAREHOUSE = 4;       //待送仓
    const STATUS_HAS_ARRIVED = 5;               //已到仓
    const STATUS_DISCARD = 6;                   //废弃
    /**预约单的主状态**/

    /**下载状态**/
    const STATUS_PROCESSING = 1;        //处理中
    const STATUS_PROCESSED = 2;        //已处理
    const STATUS_FAIL = 3;             //失败
    /**下载状态**/

    /**预约状态**/
    const RESERVATION_STATUS_NOT_EFFECTIVE = 1;       //未生效
    const RESERVATION_STATUS_EFFECTIVE = 2;          //已生效
    const RESERVATION_STATUS_EXPIRED = 3;            //已过期
    const RESERVATION_STATUS_END = 4;                //完结
    /**预约状态**/

    /**清理周期**/
    const THREE_DAYS = 1;     //3D
    const SEVEN_DAYS =2;      //7D
    const FIFTEEN_DAYS =3;    //15D
    const Thirty_DAYS =4;     //30D
    /**清理周期**/

    /**还柜状态**/
    const RETURN_STATUS_UNLOADING = 1;               //待卸柜
    const RETURN_STATUS_ALREADY = 2;                //已卸柜
    const RETURN_STATUS_RETURN_UNLOADING = 3;       //待还柜
    const RETURN_STATUS_RETURN_END = 4;             //已还柜
    /**还柜状态**/

    /**操作类型**/
    const OPERATOR_TYPE_ADD  = 1 ;  //新增
    const OPERATOR_TYPE_EDIT = 2 ; //编辑
    /**操作类型**/

    /**接口日志管理-接口类型*/
    const API_TYPE_PULL = 0; //拉取
    const API_TYPE_PUSH = 1; //推送
    const API_TYPE_RECEIVE = 2; //接收




}