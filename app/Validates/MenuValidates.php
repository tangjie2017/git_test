<?php
namespace App\Validates;


/**
 * 菜单管理表单验证
 */
class MenuValidates
{

    public static function getRulesCreate()
    {
        return [
            'route_name' => 'required|max:50',
            'en_name' => 'max:50',
            'parent_route_id' => 'required|integer'
        ];
    }

    public static function getRulesUpdate()
    {
        return [
            'route_id' => 'required|integer',
            'en_name' => 'max:50',
            'route_name' => 'required|max:50',
        ];
    }

    public static function getMessages()
    {
        return [
            'required' => ':attribute 不能为空',
            'max' => ':attribute 长度最大 :max 位',
            'integer' => ':attribute 必须是整数',
        ];
    }

    public static function getAttributes()
    {
        return [
            'route_id' => '菜单id',
            'route_name' => '名称',
            'en_name' => '英文名称',
            'url' => 'URL',
            'parent_route_id' => '上级节点'
        ];
    }

}