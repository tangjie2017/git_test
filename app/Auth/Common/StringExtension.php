<?php

namespace App\Auth\Common;


class StringExtension
{
    /** 去除空格
     * @param $param
     * @return array|string
     */
    public static function trim($param){
        if(is_string($param)){
            trim($param);
        }elseif(is_array($param)){
            foreach ($param as $k => $v) {
                $param[$k] = self::trim($v);
            }
        }elseif(is_null($param)){
            $param = '';
        }

        return $param;
    }
}