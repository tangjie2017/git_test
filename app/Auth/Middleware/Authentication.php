<?php

namespace App\Auth\Middleware;

use App\Auth\Common\AjaxResponse;
use App\Auth\Common\CurrentUser;
use Closure;
use Illuminate\Http\Request;
use App\Common\Aes;

/**
 * 授权中间件
 * Class Authentication
 * @package App\Auth\Middleware
 */
class Authentication
{
    /**
     * 处理授权
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $currentUser = CurrentUser::getCurrentUser();
        $url = $request->path();

        if (empty($currentUser)) {
            if ($request->ajax()) {
                return AjaxResponse::isSuccess(__('auth.loginExpired'), null, 2);
            }

            //判断是否PDA路由
            if (strpos($url, 'pda') === false) {
                return redirect('login');
            } else {
                return redirect('pda/login');
            }
        }

        $userPermissions = $currentUser->userPermissions;
        $allPermissions = $currentUser->allPermissions;


        //非主账号进行判断权限
        $isPermission = false;
        if (!in_array($currentUser->userCode, config('app.admin'))) {

           if ($url == '/' || $url == 'timeZone' || $url == 'getTime' || $url = 'index') { //主页
                $isPermission = false;
            } else {
                $url = $url.'/';


                //判断用户访问的当前URL已经配置到权限中
                $hasPermissions = false;
                foreach ($allPermissions as $p) {
                    if ($p == '#') {
                        continue;
                    }

                    $p = $p.'/';
                    if (strpos($url, $p) !== false) {
                        $hasPermissions = true;
                        break;
                    }
                }

                //当用户访问的当前URL已经配置到权限中
                if ($hasPermissions) {
                    $isPermission = true;
                    foreach ($userPermissions as $p) {
                        if ($p == '#') {
                            continue;
                        }

                        $p = $p.'/';
                        //有权限给设置false
                        if (strpos($url, $p) !== false) {
                            $isPermission = false;
                            break;
                        }
                    }
                }
            }
        }

        if ($isPermission) {
            if ($request->ajax()) {
                return AjaxResponse::isFailure(__('auth.accountNoPermissions'), null, 3);
            }

            abort(400, __('auth.accountNoPermissions'));
        }

        return $next($request);
    }
}