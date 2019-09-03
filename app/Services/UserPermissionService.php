<?php

namespace App\Services;

use App\Auth\Common\CurrentUser;
use App\Models\Route;
use App\Models\RouteLanguage;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\RolePermission;
use App\Auth\Common\AjaxResponse;
use Faker\Provider\DateTime;
use Illuminate\Support\Facades\DB;

class UserPermissionService
{
    /**
     * 获取用户的路由
     * @author zt12700
     * CreateTime: 2019/3/19 17:27
     * @param $id
     * @param $user_type
     * @return mixed
     */
    public static function getPermissionList($id,$role_id)
    {
        $menu = RolePermission::getPermissionList($role_id);//获取该角色下的所有路由
        $model = new UserPermission();
        $data = $model->getPermissionList($id);
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


    /**
     * 编辑用户权限（作废）
     * @param $id
     * @param $user_type
     * @param $user_route
     * @return null
     */
    public static function updateRoutesByIdAndRoutes($id ,$user_type , $user_route)
    {

        $time = date('Y-m-d H:i:s');
        $insertArr = [];
        $userPermission = UserPermission::where(['user_id'=>$id,'user_type'=>$user_type])->get()->toArray();
        $userPermission = array_column($userPermission,'route_permission');
        if(!$user_route){
            return UserPermission::where(['user_id'=>$id,'user_type'=>$user_type])->delete();
        }
        $userRoute = array_keys($user_route);
        $diff1 = array_diff($userPermission,$userRoute);//取消的权限
        $diff2 = array_diff($userRoute,$userPermission);//添加的权限

        $content = array();
        $chuArr = array();
        if($diff1){
            foreach($diff1 as $v){
                $rId = Route::where('route_id',$v)->first();
                if($rId['parent_route_id'] == 0){
                    continue;
                }else{
                    $pId=Route::where('route_id',$rId['parent_route_id'])->first();
                    if($pId['parent_route_id'] == 0){
                        continue;
                    }else{
                        $chuArr[] = $v;
                    }
                }
            }
        }

        $addArr = array();
        if($diff2){
            foreach($diff2 as $v){
                $rId = Route::where('route_id',$v)->first();
                if($rId['parent_route_id'] == 0){
                    continue;
                }else{
                    $pId=Route::where('route_id',$rId['parent_route_id'])->first();
                    if($pId['parent_route_id'] == 0){
                        continue;
                    }else{
                        $addArr[] = $v;
                    }
                }
            }
        }

        if($chuArr){
            foreach($chuArr as $k=>$v){
                $Id = Route::where('route_id',$v)->first();//获取三级的Pid
                $rId = Route::where('route_id',$Id['parent_route_id'])->first();//获取二级的Pid
                $name= RouteLanguage::where('route_id',$v)->first();//三级名称
                $rName = RouteLanguage::where('route_id',$Id['parent_route_id'])->first();//获取二级名称
                $pName = RouteLanguage::where('route_id',$rId['parent_route_id'])->first();//获取一级名称
                $content[] .= __('auth.cancel').$pName['route_name'].'-'.$rName['route_name'].'-'.$name['route_name'].__('auth.permission');
            }
        }

        if($addArr){
            foreach($addArr as $k=>$v){
                $Id = Route::where('route_id',$v)->first();
                $rId = Route::where('route_id',$Id['parent_route_id'])->first();
                $name= RouteLanguage::where('route_id',$v)->first();
                $rName = RouteLanguage::where('route_id',$Id['parent_route_id'])->first();
                $pName = RouteLanguage::where('route_id',$rId['parent_route_id'])->first();
                $content[] .= __('auth.add').$pName['route_name'].'-'.$rName['route_name'].'-'.$name['route_name'].__('auth.permission');
            }
        }

        $insArr = array();
        if($addArr || $chuArr){
            $passive_user_name = User::where('user_id',$id)->first()->toArray();
            $insArr[] = [
                'operator_user_id' => CurrentUser::getCurrentUser()->userId,
                'operator_user_name' => CurrentUser::getCurrentUser()->userCode,
                'passive_user_id' => $id,
                'passive_user_name' =>$passive_user_name['user_code'],
                'content' => join("；",$content),
                'created_at' => $time,
            ];
        }

        foreach ($user_route as $k => $v) {
            if ($v == 'on') {
                $insertArr[] = [
                    'user_id' => $id,
                    'route_permission' => $k,
                    'user_type' => $user_type,
                    'created_at' => $time,
                    'updated_at' => $time,
                ];
            }
        }

        return UserPermission::updateRoutesByIdAndRoutes($id ,$user_type ,$insertArr,$insArr);
    }

    /**
     * 复制用户权限
     * @author zt12700
     * @param array $BeReplicatedUser  被复制的用户
     * @param string $ReplicatedUser  复制的用户
     * @param int $user_id  当前登录的用户id
     * @return bool
     */
    public static function copyPermission($BeReplicatedUser,$ReplicatedUser,$user_id)
    {
        return UserPermission::copyPermission($BeReplicatedUser,$ReplicatedUser,$user_id);
    }

    /**
     * 用户默认权限（默认权限在数组里加）
     * @author zt6768
     * @return array
     */
    public static function getDefaultPermission()
    {
        return [
            20
        ];
    }

    /**
     * 获取用户绑定权限
     * @author zt6768
     * @param int $userId 用户id
     * @return array
     */
    public static function getUserPermission($userId)
    {
        return UserPermission::getUserPermission($userId);
    }

    /**
     * 给用户添加默认权限
     * @author zt12700
     * @param int $userId 用户id
     * @param array $permission 权限id
     * @return boolean
     */
    public static function addDefaultUserPermission($userId, $permission)
    {
        $nowTime = date('Y-m-d H:i:s');
        $data = [];

        foreach ($permission as $key => $routeId) {
            $data[$key]['user_id'] = $userId;
            $data[$key]['route_permission_id'] = $routeId;
            $data[$key]['created_at'] = $nowTime;
            $data[$key]['updated_at'] = $nowTime;
        }

        return UserPermission::addDefaultUserPermission($data);
    }

    /**
     * 根据用户id分配权限，并且记录日志
     * @author zt6768
     * @param int $userId 用户id
     * @param array $routeIds 表单提交路由id
     * @return boolean
     */
    public static function distributionPermission($userId, $routeIds)
    {
        if (empty($routeIds)) {
            $routeIds = [];
        }

        $userPermission = self::getUserPermission($userId);

        $collectUserPermission = [];
        $addPermissionId = [];
        $cancelPermissionId = [];

        //收集新增权限id
        $time = date('Y-m-d H:i:s');
        if ($routeIds) {
            foreach ($routeIds as $key => $routeId) {
                if (!in_array($routeId, $userPermission)) {
                    $addPermissionId[] = $routeId;
                }

                $collectUserPermission[$key]['user_id'] = $userId;
                $collectUserPermission[$key]['route_permission_id'] = $routeId;
                $collectUserPermission[$key]['created_at'] = $time;
                $collectUserPermission[$key]['updated_at'] = $time;
            }
        }

        //收集取消权限id
        if ($userPermission) {
            foreach ($userPermission as $permissionId) {
                if (!in_array($permissionId, $routeIds)) {
                    $cancelPermissionId[] = $permissionId;
                }
            }

        }

        $addCancelPermissionId = array_merge($addPermissionId, $cancelPermissionId);

        //分配权限日志
        $userLog = [];
        if ($addCancelPermissionId) {
            $routes = RouteService::getListByRouteId($addCancelPermissionId);

            //获取当前路由名称
            $currentRouteNames = RouteService::routeLanguageKeyToValue($addCancelPermissionId);

            //获取父节点路由名称
            $parentRouteNames = RouteService::getParentRouteNameByRouteId($routes);

            $currentUser = CurrentUser::getCurrentUser();

            $operatorUserId = $currentUser->userId;
            $operatorUserName = $currentUser->userCode;

            $conditon = [];
            $conditon['user_id'] = $userId;
            $userInfo = UserService::getInfoByConditon($conditon);
            foreach ($routes as $key => $item) {
                $userLog[$key]['operator_user_id'] = $operatorUserId;
                $userLog[$key]['operator_user_name'] = $operatorUserName;
                $userLog[$key]['passive_user_id'] = $userId;
                $userLog[$key]['passive_user_name'] = $userInfo->user_code;
                $userLog[$key]['created_at'] = $time;

                $logContent = '';
                //添加权限日志
                if (in_array($item->route_id, $addPermissionId)) {
                    $logContent .= __('auth.add');

                    //用中文记录
                    if (isset($parentRouteNames[$item->parent_route_id]['zh_CN'])) {
                        $logContent .= $parentRouteNames[$item->parent_route_id]['zh_CN'].'-';
                    }

                    if (isset($currentRouteNames[$item->route_id]['zh_CN'])) {
                        $logContent .= $currentRouteNames[$item->route_id]['zh_CN'];
                    }

                    $logContent .= __('auth.permission');
                }

                //取消权限日志
                if (in_array($item->route_id, $cancelPermissionId)) {
                    $logContent .= __('auth.cancel');

                    //用中文记录
                    if (isset($parentRouteNames[$item->parent_route_id]['zh_CN'])) {
                        $logContent .= $parentRouteNames[$item->parent_route_id]['zh_CN'].'-';
                    }

                    if (isset($currentRouteNames[$item->route_id]['zh_CN'])) {
                        $logContent .= $currentRouteNames[$item->route_id]['zh_CN'];
                    }

                    $logContent .= __('auth.permission');
                }

                $userLog[$key]['content'] = $logContent;
            }
        }

        return UserPermission::updateRoutesByIdAndRoutes($userId, $collectUserPermission, $userLog);
    }

}