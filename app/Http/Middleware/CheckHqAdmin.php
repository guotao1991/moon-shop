<?php

namespace App\Http\Middleware;

use App\Exceptions\LoginException;
use App\Traits\JsonResponse;
use App\Utils\Helper;
use Closure;
use Exception;
use Illuminate\Http\Request;

class CheckHqAdmin
{
    use JsonResponse;

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $admin = Helper::admin();
        $flag = $admin->isHqAdmin();

        if ($flag) {
            throw new LoginException("请重新登录");
        }

        return $next($request);
    }
}
