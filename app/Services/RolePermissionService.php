<?php
/**
 * @author zt12700
 * CreateTime: 2019/5/9 17:29
 *
 */

namespace App\Services;

use App\Models\RolePermission;
use App\Models\Route;

class RolePermissionService
{
    /**
     * 获取角色的路由
     * @author zt12700
     * @param $id
     * @return mixed
     */
    public static function getPermissionList($id)
    {
        $menu = Route::getMenuData();//获取所有路由
        $model = new RolePermission();
        $data = $model->getPermissionList($id); //根据角色id获取路由

        return self::getMenuTreeListByData($menu,$data);
    }

    /**
     * 获取目录树结构
     * @author zt7242
     * @date 2019/4/30 18:27
     * @param $meun  所有路由
     * @param $data  角色路由
     * @return array
     */
    public static function getMenuTreeListByData($menu,$data=[])
    {
        $ids = [];
        foreach ($data as $value){
            //将角色路由id取出来放在数组中
            $ids[]=$value['id'];
        }

        $items = array();
            foreach ($menu as $v){
                //加入前端显示需要的参数
                if(in_array($v['id'],$ids)){
                    if($v['parentId'] == 0){
                        $v['checkArr'] = [["type"=>"0", "isChecked"=>"1"]];
                    }else{
                        $v['checkArr'] = "1";
                    }
                }else{
                    if($v['parentId'] == 0){
                        $v['checkArr'] = [["type"=>"0", "isChecked"=>"0"]];
                    }else{
                        $v['checkArr'] = "0";
                    }
                }
                $items[$v['id']] = $v;
            }

        //遍历数据 生成树状结构
        $tree = array();
        foreach($items as $key => $item){
            if(isset($items[$item['parentId']])){
                $items[$item['parentId']]['children'][] = &$items[$key];
            }else{
                $tree[] = &$items[$key];
            }
        }
        return $tree;

    }

}