<?php

namespace App\Services;

use App\Auth\Common\CurrentUser;
use App\Models\Route;
use App\Models\RouteLanguage;
use App\Models\StaticState;

class RouteService
{

    /**
     * 获取目录数据
     * @author zt7242
     * @date 2019/4/30 18:28
     * @return array
     */
    public static function getMenuData()
    {
        $data =  Route::getMenuData();
        return self::getMenuTreeListByData($data);
    }

    /**
     * 获取目录树结构
     * @author zt7242
     * @date 2019/4/30 18:27
     * @param $data
     * @return array
     */
    public static function getMenuTreeListByData($data)
    {
        $items = array();
        foreach($data as $value){
            //加入前端显示需要的参数
            if($value['parentId'] == 0){
                $value['checkArr'] = [["type"=>"0", "isChecked"=>"0"]];
            }else{
                $value['checkArr'] = "0";
            }
            $items[$value['id']] = $value;
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


    /**
     * 新增或编辑保存数据
     * @author zt7242
     * @date 2019/5/6 11:20
     * @param $data
     * @return array|bool
     */
    public static function saveData($data)
    {
        return Route::saveData($data);
    }


    /**
     * 首页根据权限获取路由
     * @author zt7239
     * @param int $user_id 用户id
     * @return array
     */
    public static function getNavigationNodeByUserId($user_id)
    {
        $lan = session()->get('lang') ? session()->get('lang') : "zh_CN";
        $userPre = UserPermissionService::getUserPermission($user_id);
        $userCode = CurrentUser::getCurrentUser()->userCode;

        if($lan == "zh_CN"){
            $model = Route::leftJoin('route_language','route.route_id','=','route_language.route_id')->select('route.*','route_language.route_name as title');
        }else{
            $model = Route::leftJoin('route_language','route.route_id','=','route_language.route_id')->select('route.*','route_language.en_name as title');
        }


        //主账号菜单不受限制
        if (in_array($userCode, config('app.admin'))) {
            $result = $model->orderBy('route.sort')->get()->toArray();
        } else {
            $result = $model->whereIn('route.route_id', $userPre)->orderBy('route.sort')->get()->toArray();
        }

        return self::listToTree($result, 'route_id', 'parent_route_id', 'children', 0);
    }

    /**
     * 获取菜单树结构
     * @author zt7239
     * @param $list
     * @param string $pk   为 route_id
     * @param string $pid  为 parent_route_id
     * @param string $child  自定义树结构的child层
     * @param int $root
     * @return array
     */
    public static function listToTree($list, $pk = 'route_id', $pid = 'parent_route_id', $child = 'children', $root = 0)
    {
        $tree = array();
        if(is_array($list)) {
            //创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                //判断是否存在parent
                $parentId =  $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    } else {
                        $tree[] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }


    /**
     * 通过id获取该路由信息
     * @author zt7239
     * @param $id
     * @return mixed
     */
    public static function getMenuInfoById($id = 0)
    {
        return Route::getMenuInfoById($id);
    }

    /**
     * 通过id删除该条路由信息
     * @author zt7242
     * @date 2019/5/6 10:53
     * @param $strIds
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public static function delMenuInfo($strIds)
    {
        if(empty($strIds)) return false;
        return Route::delMenuInfo($strIds);
    }


    /**
     * 根据路由id获取路由
     * @author zt6768
     * @param array $routeIds 路由id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getListByRouteId($routeIds)
    {
        return Route::getListByRouteId($routeIds);
    }

    /**
     * 路由数据转换成"路由id => 路由URL"格式
     * @author zt6768
     * @param array $routeIds 路由id
     * @return array
     */
    public static function routeDateKeyToValue($routeIds)
    {
        $route = self::getListByRouteId($routeIds);

        $data = [];

        foreach ($route as $item) {
            $data[$item->route_id] = $item->url;
        }

        return $data;
    }

    /**
     * 分类RMS路由、PDA路由
     * @author zt12700
     * @return array
     */
    public static function getRmsOrPdaRoute()
    {
        $routes = self::getNavigations();

        $categoryRoute = [];
        foreach ($routes as $route) {
            if (strtoupper($route['route_name']) == 'PDA') { //PDA路由
                $categoryRoute['PDA'][] = $route;
            } else { //LMS路由
                $categoryRoute['RMS'][] = $route;
            }
        }

        unset($routes);

        return $categoryRoute;
    }

    /**
     * 获取所有节点
     * @author zt12700
     * CreateTime: 2019/3/19 10:48
     * @return array
     */
    public static function getNavigations()
    {
        //所有路由
        $node = Route::getAllRoute();
        $arr = [];
        //组装一级菜单
        foreach ($node as $k => $value) {
            if ($value['parent_route_id'] == 0) {
                $arr[] = $value;
                unset($node[$k]);
            }
        }

        if (!$arr) {
            return [];
        }

        //组装二级菜单
        foreach ($arr as $ak => $av) {
            foreach ($node as $k => $v) {
                if ($v['parent_route_id'] == $av['route_id']) {
                    $arr[$ak]['child'][] = $v;
                    unset($node[$k]);
                }
            }
        }

        //组装三级菜单
        foreach ($arr as $ak => $av) {
            if(isset($av['child'])){
                foreach ($av['child'] as $aak => $aav) {
                    foreach ($node as $k => $v) {
                        if ($v['parent_route_id'] == $aav['route_id']) {
                            $arr[$ak]['child'][$aak]['child'][] = $v;
                            unset($node[$k]);
                        }
                    }
                }
            }else{
                continue;
            }

        }

        return $arr;
    }

    /**
     * 路由名称数据转换成"路由id => 路由URL"格式
     * @author zt6768
     * @param array $routeIds 路由id
     * @return array
     */
    public static function routeLanguageKeyToValue($routeIds)
    {
        $routeLanguage = RouteLanguage::getListByRouteIds($routeIds);

        $data = [];

        foreach ($routeLanguage as $item) {
            $data[$item->route_id][$item->language] = $item->route_name;
        }

        unset($routeLanguage);

        return $data;
    }

    /**
     * 获取父节点路由名称
     * @author zt6768
     * @param object  子节点信息
     * @return array
     */
    public static function getParentRouteNameByRouteId($routes)
    {
        if ($routes->count() == 0) {
            return [];
        }

        //父节点路由id
        $routeIdsToArray = $routes->toArray();
        $parentRouteIds = array_column($routeIdsToArray, 'parent_route_id');
        $parentRouteIds = array_unique($parentRouteIds);
        unset($routeIdsToArray);

        //父节点路由名称
        $routeLanguageKeyToValue = self::routeLanguageKeyToValue($parentRouteIds);

        $parentRouteNames = [];
        foreach ($routes as $item) {
            if (isset($routeLanguageKeyToValue[$item->parent_route_id])) {
                $parentRouteNames[$item->parent_route_id] = $routeLanguageKeyToValue[$item->parent_route_id];
            }
        }

        return $parentRouteNames;
    }


    /**
     * 路由URL数据转换成"路由id => 路由URL"格式
     * @author zt6768
     * @return array
     */
    public static function getRouteUrl()
    {
        $routes = Route::getALL();

        $data = [];
        foreach ($routes as $route) {
            $data[$route->route_id] = $route->url;
        }

        unset($routes);

        return $data;
    }

}