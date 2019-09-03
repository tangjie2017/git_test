<?php

namespace App\Auth\Common;
class Response
{
    /** 消息
     * @var string
     */
    public $message;
    /** 状态（true成功，false失败）
     * @var bool
     */
    public $status;

    /** 数据
     * @var object
     */
    public $data;

    /** 返回成功
     * @param null $data 数据
     * @return Response
     */
    public static function isSuccess($data = null){
        $response = new Response();
        $response->status = true;
        $response->data = $data;
        return $response;
    }

    /** 返回失败
     * @param $message 消息
     * @return Response
     */
    public static function isFailure($message){
        $response = new Response();
        $response->status = false;
        $response->message = $message;
        return $response;
    }
}