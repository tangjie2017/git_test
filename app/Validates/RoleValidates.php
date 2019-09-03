<?php
/**
 * @author zt12700
 * CreateTime: 2019/5/8 18:15
 *
 */

namespace App\Validates;


class RoleValidates
{
    /**
     * 新增验证
     * @author zt12700
     * @return array
     */
    public static function addRule()
    {
        return [
            'role_name' => 'required|between:1,50|unique:role',
            'en_name' => 'required|between:1,50'

        ];
    }

    /**
     * 编辑验证
     * @author zt12700
     * @param $id
     * @return array
     */
    public static function updateRule($id)
    {
        return [
            'role_name' => "required|between:1,50|unique:role,role_name,".$id.',role_id',
            'en_name' => 'required|between:1,50'
        ];
    }


    /**
     * 提示错误信息
     * @author zt12700
     * @return array
     */
    public static function getMessages()
    {
        return [
            'required' => ':attribute '.__('auth.canNotBeEmpty'),
            'between' => ':attribute'.__('auth.BitsLength'),
            'unique' => ":attribute".__('auth.CannotRepeat'),
        ];
    }

    /**
    * 属性名称
    * @author zt12700
    * @return array
    */
    public static function getAttributes()
    {
        return [
            'role_name' => __('auth.RoleName'),
            'en_name' => __('auth.EnglishName'),
        ];
    }
}