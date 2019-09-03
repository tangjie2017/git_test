<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class Language
{
    /**
     * 语言包中间件注册
     * @author zt6768
     */
    public function handle(Request $request, Closure $next)
    {
        $language = $request->input('language');
        if ($language) {
            $request->session()->put('lang', $language);
            App::setLocale($language);
        } else {
            $language = $request->session()->get('lang');
            $language = $language ? $language : App::getLocale();
            App::setLocale($language);
        }

        return $next($request);
    }
}
