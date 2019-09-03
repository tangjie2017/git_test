<?php

namespace App\Http\Middleware;

use App\Auth\Common\AjaxResponse;
use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'email',
        'api/containerDetail',
        'api/reservationCreate',//谷仓创建接口
        'api/reservationUpdate',//谷仓编辑接口
        'api/updateReservationStatus',//谷仓更新状态接口
        'api/searchReservationOrder',//谷仓查询预约单接口
        'api/reservationNumberDetail',//谷仓查看预约单详情接口
        'api/orderTimer',
        'api/trackingTimer',
        'upload/file',
        'reservation_management/upload',
        'reservation_management/searchInbound',
        'reservation_code/index'
    ];

    /**
     * token有效期验证
     * @author zt6768
     */
    public function handle($request, Closure $next)
    {
        $isExcept = in_array($request->path(), $this->except);

        if ($request->isMethod('post')) {
            $sessionToken = $request->session()->token();
            $inputToken = $request->input('_token');

            //toke过期
            if ($sessionToken != $inputToken && !$isExcept) {
                if ($request->ajax()) {
                    return AjaxResponse::isFailure(__("auth.tokenExpired"), null, 4);
                }

                return back()->with(["message" => __("auth.tokenExpired")]);
            }
        }

        //禁用CSRF
        if ($isExcept) {
            return $next($request);
        }

        //使用CSRF
        return parent::handle($request, $next);
    }
}
