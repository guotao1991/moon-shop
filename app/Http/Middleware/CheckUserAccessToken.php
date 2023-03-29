<?php

namespace App\Http\Middleware;

use App\Exceptions\LoginException;
use App\Exceptions\NotesException;
use App\Services\V1\SystemService;
use App\Services\V1\User\UserService;
use App\Traits\JsonResponse;
use Exception;
use Illuminate\Http\Request;
use Closure;

class CheckUserAccessToken
{
    use JsonResponse;

    protected $userService;
    protected $systemService;

    /**
     * CheckUserAccessToken constructor.
     * @param UserService $userService
     * @param SystemService $systemService
     */
    public function __construct(
        UserService $userService,
        SystemService $systemService
    ) {
        $this->userService = $userService;
        $this->systemService = $systemService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws NotesException
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        //获取用户TOKEN
        $token = (string)$request->header("Authorization", "");

        if (empty($token)) {
            throw new LoginException("用户未登录，请重新登录");
        }

        //验证IP黑名单
        $res = $this->systemService->isInBlackList($request->ips());
        if ($res) {
            throw new NotesException("请求被限制");
        }

        //验证token
        $userInfo = $this->userService->getUserByToken($token);
        session()->put("user.info", $userInfo);

        return $next($request);
    }
}
