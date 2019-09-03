<?php
namespace App\Http\Api\Soap;

class Common
{
    /**
     * 对象转数组
     * @param $obj
     * @return mixed
     */
    public static function objectToArray($obj)
    {
        $arr = '' ;
        $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
        if(is_array($_arr)){
            foreach($_arr as $key => $val){
                $val = (is_array($val) || is_object($val)) ? self::objectToArray($val) : $val;
                $arr[$key] = $val;
            }
        }
        return $arr;
    }
}