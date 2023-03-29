<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Admin\Account\AddManagerRequest;
use App\Http\Requests\V1\Admin\Account\ForgetPassRequest;
use App\Http\Requests\V1\Admin\Account\LoginByPassRequest;
use App\Http\Requests\V1\Admin\Account\LoginByWxRequest;
use App\Http\Requests\V1\Admin\Account\LoginRequest;
use App\Http\Requests\V1\Admin\Account\UpdatePassRequest;
use App\Http\Resources\V1\Admin\AdminResource;
use App\Http\Resources\V1\User\UserResource;
use App\Models\Admin\AdminModel;
use App\Models\User\UserModel;
use App\Services\V1\AdminService;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Throwable;

class AccountController extends Controller
{
    protected $adminService;

    /**
     * UserController constructor.
     * @param AdminService $userService
     */
    public function __construct(AdminService $userService)
    {
        $this->adminService = $userService;
    }

    /**
     * 用户登录/没有就注册
     *
     * @param LoginRequest $request
     * @return mixed
     * @throws Throwable
     */
    public function loginByCode(LoginRequest $request)
    {
        $data = $request->validated();
        $admin = $this->adminService->mobileLogin($data);

        return $this->success(new AdminResource($admin));
    }

    /**
     * 用户登录/账号密码登录
     *
     * @param LoginByPassRequest $request
     * @return mixed
     * @throws Throwable
     */
    public function loginByPass(LoginByPassRequest $request)
    {
        $data = $request->validated();
        $admin = $this->adminService->passLogin($data);

        return $this->success(new AdminResource($admin));
    }

    /**
     * 微信登录
     *
     * @param LoginByWxRequest $request
     * @return mixed
     * @throws Throwable
     */
    public function loginByWx(LoginByWxRequest $request)
    {
        $data = $request->validated();
        list($isNew, $goPage, $user) = $this->adminService->wxLogin($data);

        return $this->success(['is_new' => $isNew, "go_page" => $goPage, "uer_info" => new UserResource($user)]);
    }

    /**
     * 模拟登录
     * @return array|mixed
     * @throws Exception
     */
    public function loginDev()
    {
        list($isNew, $goPage, $user) = $this->adminService->devLogin();

        return $this->success(['is_new' => $isNew, "go_page" => $goPage, "uer_info" => new UserResource($user)]);
    }

    /**
     * 忘记密码，修改密码
     * @param ForgetPassRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function forgetPass(ForgetPassRequest $request)
    {
        $data = $request->validated();
        $res = $this->adminService->forgetPass($data);
        if (!$res) {
            return $this->failed("修改失败");
        }

        return $this->success();
    }

    /**
     * 修改密码
     * @param UpdatePassRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function updatePass(UpdatePassRequest $request)
    {
        $data = $request->validated();
        $res = $this->adminService->updatePass($data);
        if (!$res) {
            return $this->failed("修改失败");
        }

        return $this->success();
    }

    /**
     * 获取账号类型
     *
     * @param int $storeId 店铺Id
     * @return array|mixed
     * @throws Exception
     */
    public function adminType(int $storeId)
    {
        $type = $this->adminService->adminTypeByStoreId($storeId);

        return $this->success(["type" => $type]);
    }

    /**
     * 添加管理员
     *
     * @param AddManagerRequest $request
     * @return array|mixed
     * @throws Throwable
     */
    public function addManager(AddManagerRequest $request)
    {
        $data = $request->validated();
        $managerInfo = $this->adminService->addManager($data);

        return $this->success(new AdminResource($managerInfo));
    }
}
