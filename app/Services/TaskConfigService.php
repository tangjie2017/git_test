<?php

namespace App\Services;

use App\Auth\Common\AjaxResponse;
use App\Models\DownloadCleanup;
use App\Models\TaskConfig;

class TaskConfigService
{


    public static function insertAndValidateConfig($data)
    {

        //整理数据并验证
        $pattern_tel = "/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/";
        $pattern_email = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";

        $data['is_notice_open'] = isset($data['is_notice_open'])?$data['is_notice_open']:0;
        $data['message_open'] = isset($data['message_open'])?$data['message_open']:0;
        $data['message_notice_supplier'] = isset($data['message_notice_supplier'])?$data['message_notice_supplier']:0;
        $data['email_open'] = isset($data['email_open'])?$data['email_open']:0;
        $data['email_notice_supplier'] = isset($data['email_notice_supplier'])?$data['email_notice_supplier']:0;

        if(strlen($data['remaining_time']) > 5 || strlen($data['over_time']) > 5 ){
            return AjaxResponse::isFailure(__('auth.NoMoreThanFiveCharacters'),'');
        }

        if(!empty($data['remaining_time'] && empty($data['interval_time']))){
            return AjaxResponse::isFailure(__('auth.ReminderIntervalRequired'),'');
        }

        if(!empty($data['remaining_time'] && empty($data['frequency']))){
            return AjaxResponse::isFailure(__('auth.RemindersRequired'),'');
        }
        $mes = [];
        $email = [];
        $time = date('Y-m-d H:i:s',time());
        foreach($data['messageName'] as $k => $v){
            if(!empty($v) && empty($data['messageTel'][$k])){
                return AjaxResponse::isFailure(__('auth.fillRecipientTel'),'');
            }

            if(!empty($data['messageTel'][$k]) && empty($v)){
                return AjaxResponse::isFailure(__('auth.fillrecipientName'),'');
            }

            if(empty($data['messageTel'][$k]) && empty($v)){
                continue;
            }

            if(!preg_match($pattern_tel,$data['messageTel'][$k])){
                return AjaxResponse::isFailure(__('auth.fillCorrectNumber'),'');
            };

            if(strlen($data['messageName'][$k])>30){
                return AjaxResponse::isFailure(__('auth.noMoreThanThirtyCharacters'),'');
            };
            $mes[$k]['origin'] = 1;
            $mes[$k]['consignee_name'] = $v;
            $mes[$k]['consignee_telephone'] = $data['messageTel'][$k];
            $mes[$k]['consignee_email'] = null;
            $mes[$k]['created_at'] = $time;
            $mes[$k]['updated_at'] = $time;
        }
        //短信收件人为空，提示供应商关闭，但短信配置开启的情况
        if(empty($mes) && empty($data['message_notice_supplier']) && !empty($data['message_open'])){
            return AjaxResponse::isFailure(__('auth.noSMSRecipientsToNotify'),'');
        }
        unset($data['messageName']);
        unset($data['messageTel']);


        foreach($data['emailName'] as $key => $value){
            if(!empty($value) && empty($data['email'][$key])){
                return AjaxResponse::isFailure(__('auth.fillrecipientMail'),'');
            }

            if(!empty($data['email'][$key]) && empty($value)){
                return AjaxResponse::isFailure(__('auth.fillrecipientName'),'');
            }

            if(empty($data['email'][$key]) && empty($value)){
                continue;
            }

            if(!preg_match($pattern_email,$data['email'][$key])){
                return AjaxResponse::isFailure(__('auth.fillCorrectMail'),'');
            };

            if(strlen($data['emailName'][$key])>30){
                return AjaxResponse::isFailure(__('auth.noMoreThanThirtyCharacters'),'');
            };

            $email[$key]['origin'] = 2;
            $email[$key]['consignee_name'] = $value;
            $email[$key]['consignee_email'] = $data['email'][$key];
            $email[$key]['consignee_telephone'] = null;
            $email[$key]['created_at'] = $time;
            $email[$key]['updated_at'] = $time;
        }
        //邮件收件人为空，提示供应商关闭，但邮件配置开启的情况
        if(empty($email) && empty($data['email_notice_supplier']) && !empty($data['email_open'])){
            return AjaxResponse::isFailure(__('auth.NoMessageRecipientsToNotified'),'');
        }
        unset($data['emailName']);
        unset($data['email']);

        $consignee = array_merge($mes,$email);
        //将数据插入task_config表和consignee_notice_list表
        $task_config = TaskConfig::insertConfig($data,$consignee);
        if($task_config){
            return AjaxResponse::isSuccess(__('auth.saveSuccess'),'');
        }else{
            return AjaxResponse::isSuccess(__('auth.saveFailure'),'');
        }

    }

    /**
     * 任务配置-删除周期保存
     * @author zt7242
     * @date 2019/4/28 11:44
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function saveCycleConfig($data)
    {
        $cycle = DownloadCleanup::saveCycleConfig($data);
        if($cycle){
            return AjaxResponse::isSuccess(__('auth.saveSuccess'),'');
        }else{
            return AjaxResponse::isSuccess(__('auth.saveFailure'),'');
        }
    }

    /**
     * 获取配置信息
     * @author zt7242
     * @date 2019/4/26 19:39
     * @return mixed
     */
    public static function getConfigInfo()
    {
        return TaskConfig::getConfigInfo();
    }

    /**
     * 获取下载删除周期配置信息
     * @author zt7242
     * @date 2019/4/28 13:09
     * @return mixed
     */
    public static function getCycleInfo()
    {
        return DownloadCleanup::getCycleInfo();
    }
    /**
     * 提醒次数
     * @author zt7242
     * @date 2019/4/26 17:27
     * @return array
     */
    public static function getFrequency()
    {
        return [
            1=>1,
            2=>2,
            3=>3,
            4=>4,
            5=>5,
            6=>6,
            7=>7,
            8=>8,
            9=>9,
            10=>10,
        ];
    }

    public static function getCycle()
    {
        return [
            1=>'3D',
            2=>'7D',
            3=>'15D',
            4=>'30D',
        ];
    }

}