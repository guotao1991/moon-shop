<?php

namespace App\Http\Middleware;

use Closure;

class EnableCrossRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        /*$origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        //允许访问
        $allow_origin = [
            "*"
        ];

        if (app()->environment() == 'local') {
            $response->header('Access-Control-Allow-Origin', "*");
        } elseif (in_array($origin, $allow_origin)) {
            $response->header('Access-Control-Allow-Origin', $origin);
        }

        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
        $response->header('Access-Control-Expose-Headers', 'Authorization, authenticated');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Credentials', 'true');
        $response->header('Access-Control-Max-Age', 86400); // 时间为24小时 单位：秒*/
        return $response;
    }
}
