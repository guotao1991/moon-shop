<?php

namespace App\Http\Middleware;

use App\Exceptions\LoginException;
use App\Exceptions\NotesException;
use App\Models\User\UserModel;
use App\Services\V1\AdminService;
use App\Traits\JsonResponse;
use App\Utils\Helper;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Http\Request;
use Closure;

class CheckAdmin
{
    use JsonResponse;

    protected $adminService;

    /**
     * @param AdminService $adminService
     */
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Helper::user();
        if (empty($user)) {
            throw new LoginException("用户未登录，请重新登录");
        }

        if ($user->is_authorized == UserModel::AUTHORIZED_FALSE) {
            throw new LoginException("您未被授权使用商家端");
        }

        $admin = $user->admin;

        if (empty($admin)) {
            //增加商家管理账号
            $this->adminService->addAdmin();
        }

        session()->put("admin.info", $admin);

        return $next($request);
    }
}
