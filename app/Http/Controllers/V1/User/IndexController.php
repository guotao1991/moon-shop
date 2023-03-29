<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\Index\AuthorizationMobileRequest;
use App\Http\Requests\V1\User\Index\EditRequest;
use App\Http\Resources\V1\User\StoreUserResource;
use App\Http\Resources\V1\User\UserResource;
use App\Services\V1\User\UserService;
use Exception;

class IndexController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 用户编辑自己的资料
     * @param EditRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function edit(EditRequest $request)
    {
        $data = $request->validated();
        $this->userService->editUser($data);

        return $this->success([], "操作成功");
    }

    /**
     * 用户获取自己的资料信息
     * @return array
     * @throws Exception
     */
    public function userInfo()
    {
        $info = $this->userService->storeUserInfo();
        return $this->success(new StoreUserResource($info));
    }

    /**
     * 用户授权手机号
     * @param AuthorizationMobileRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function authorizationMobile(AuthorizationMobileRequest $request)
    {
        $data = $request->validated();
        $mobile = $this->userService->authorizationMobile($data);
        return $this->success($mobile);
    }
}
