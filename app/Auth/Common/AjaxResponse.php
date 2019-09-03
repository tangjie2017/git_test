<?php

namespace App\Auth\Common;


/** ajax请求响应实体
 * Class AjaxResponse
 * @package App\Auth\Common
 */
class AjaxResponse
{
    /** 状态（0失败，1成功）
     * @var int
     */
    public $Status;
    /**
     * @var string
     */
    public $Message;
    /**
     * @var
     */
    public $Data;

    /** 返回成功的ajax响应
     * @param string $message 消息
     * @param null $data 数据
     * @return \Illuminate\Http\JsonResponse
     */
    public static function isSuccess($message=null, $data=null){
        $ajaxResponse = new AjaxResponse();
        $ajaxResponse->Status = 1;
        $ajaxResponse->Message = $message;
        $ajaxResponse->Data = $data;
        return response()->json($ajaxResponse);
    }

    /** 返回失败的ajax响应
     * @param string $message 消息
     * @param null $data 数据
     * @return \Illuminate\Http\JsonResponse
     */
    public static function isFailure($message, $data=null){
        $ajaxResponse = new AjaxResponse();
        $ajaxResponse->Status = 0;
        $ajaxResponse->Message = $message;
        $ajaxResponse->Data = $data;
        return response()->json($ajaxResponse);
    }
}