<?php

namespace App\Http\Middleware;

use App\Auth\Common\AjaxResponse;
use App\Auth\Common\CurrentUser;
use Closure;
use Illuminate\Http\Request;
use App\Common\Aes;
class ReservationCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $currentUser = CurrentUser::getCurrentUser();
        if (empty($currentUser)) {
                $content = $request->input('content');

                if ($content != null) {
                    //解密

            if (strpos($content, " ")) {
                $content = str_replace(" ", "+", $content);
            }
                    $aes = new Aes(Aes::getDefaultKey());
                    $requestData = $aes->decrypt($content);

                    if ($requestData) {
                        //将content存入session
                        $request->session()->put('content', $content);
                        return $next($request);
                    } else {
                        abort(400, __('auth.NecessaryParameters'));
                    }
                } else {
                    abort(400, __('auth.NecessaryParameters'));
                }
            }else{
                 return $next($request);
            }

        }

}
