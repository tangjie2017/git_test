<?php

namespace App\Curl;

class Curl
{

    public function curlHttp($url, $data = [], $mothed = 'GET')
    {
        if ($mothed == 'POST') {
            return $this->vpost($url, $data);
        }
        return $this->vget($url ,$data);
    }


    /**
     * http get请求
     * @param string $url 请求url
     * @param array  $data 请求参数
     * @return array
     */
    public function vget($url, $data)
    {
        //初始化
        $ch = curl_init();

        if ($data) {
            $url = $url.'?'.http_build_query($data);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //尝试连接时等待的600000毫秒数
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS,600000);
        //允许执行的最长600000毫秒数
        curl_setopt($ch, CURLOPT_TIMEOUT_MS,600000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = [];
        $result['data'] = curl_exec($ch);
        //获取HTTP代码
        $result['code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        //获取错误详情
        $result['error'] = curl_error($ch);

        curl_close($ch);

        return $result;
    }

    /**
     * http post请求
     * @param string $url 请求url
     * @param array  $data 请求参数
     * @param int $setopt 1-设置HTTP请求头部"application/json"格式
     * @return array
     */
    public function vpost($url, $data, $setopt = 1)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //POST提交方式
        curl_setopt($ch, CURLOPT_POST, 1);
        //尝试连接时等待的600000毫秒数
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 600000);
        //允许执行的最长600000毫秒数
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 600000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($setopt == 1) { //json格式
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } else { //默认的
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        //线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        if (substr($url,0,5) == 'https') {
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        $result = [];
        $result['data'] = curl_exec($ch);
        $result['code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $result['error'] = curl_error($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * post请求fbg
     * @author zt12700
     * @param $url string 请求地址
     * @param $params array 请求的参数，
     * @param $authorization  string 请求头的TOKEN,通过auth接口拿token
     * @param bool $culopt_header true-输出header，false-不输出header
     * @return mixed
     */
    public function postFBG($url,$data,$authorization, $setopt = 1)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //POST提交方式
        curl_setopt($ch, CURLOPT_POST, 1);
        //尝试连接时等待的600000毫秒数
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 600000);
        //允许执行的最长600000毫秒数
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 600000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $heardArr[] = "Content-Type: application/json";
        //如果有请求头的授权TOKEN则添加进请求头里
        if($authorization){
            $heardArr[] = "Authorization".$authorization;
        }

        if ($setopt == 1) { //json格式
            curl_setopt($ch, CURLOPT_HTTPHEADER, $heardArr);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } else { //默认的
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        //线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        if (substr($url,0,5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        $result = [];
        $result['data'] = curl_exec($ch);
        $result['code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $result['error'] = curl_error($ch);
        curl_close($ch);

        return $result;

    }

    /**
     * 生成GUID
     * @author zt7242
     */
    public function create_guid()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }

    /**
     * @description加签
     * @author zt7242
     * @date 2019/4/8 9:19
     * @param $guid
     * @return string
     */
    public function sign($guid)
    {

        $token = config('api.owms.appToken');
        $sign = md5(md5($token) . $guid);
        return $sign;
    }

    /**
     * wms加签
     * @author zt7242
     * @date 2019/5/9 16:13
     * @param $request_time
     * @param $request_data
     * @return string
     */
    public function signWms($request_time,$request_data)
    {
        $key = config('api.wmsOms.wmsKey');
        $sign = md5( $request_time.$key.json_encode($request_data) );
        return $sign;
    }















}
