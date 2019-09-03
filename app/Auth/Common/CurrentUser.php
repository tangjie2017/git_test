<?php

namespace App\Auth\Common;

use App\Services\UserPermissionService;
use App\Models\UserPermission;
use Illuminate\Http\Request;

/**
 * 当前登录用户
 * Class CurrentUser
 * @package App\Auth\Common
 */
class CurrentUser
{
    const CURRENTUSER_SESSIONKEY = "__CURRENTUSER_SESSIONKEY";

    /**
     * 用户id
     * @var int
     */
    public $userId = 0;

    /**
     * 用户名称
     * @var string
     */
    public $userName;

    /**
     * 账号
     * @var string
     */
    public $userCode;

    /**
     * 用户登录时选择的仓库所在时区
     * @author zt3361
     * @var
     */
    public $wareTime;

    /**
     * 用户登录时选择的仓库所在时区（该值不会修改）
     * @author zt3361
     * @var
     */
    public $wareTimeNotUpdate;

    /**
     * 用户权限
     * 每个权限是一个key/value数组
     * @var array
     */
    public $userPermissions = [];

    /**
     * 所有权限集合
     * 每个权限是一个key/value数组
     * @var array
     */
    public $allPermissions = [];

    /**
     * 获取当前登录用户
     * @return CurrentUser
     */
    public static function getCurrentUser()
    {
        return session(CurrentUser::CURRENTUSER_SESSIONKEY);
    }

    /**
     * 设置当前登录用户
     * @param CurrentUser $currentUser
     */
    public static function setCurrentUser(CurrentUser $currentUser)
    {
        session([CurrentUser::CURRENTUSER_SESSIONKEY=>$currentUser]);
    }

    /**
     * 移除当前登录用户
     * @param Request $request
     */
    public static function removeCurrentUser(Request $request)
    {
        $request->session()->flush();
    }

    /**
     * 根据路由判断是否有权限
     * @author zt7239
     * @param string $url 路由
     * @return bool
     */
    public static function isPermissions($url)
    {
        $currentUser = CurrentUser::getCurrentUser();
        $userPermissions = $currentUser->userPermissions;

        //主账号不用判断权限
        if (in_array($currentUser->userCode, config('app.admin'))) {
            return true;
        }

        $isPermission = false;
        //防止字符串出现包含关系
        $url = $url.'/';
        foreach ($userPermissions as $p) {
            if ($p == '#') {
                continue;
            }

            $p = $p.'/';
            if (strpos($url, $p) !== false) {
                $isPermission = true;
                break;
            }
        }

        return $isPermission;
    }

}