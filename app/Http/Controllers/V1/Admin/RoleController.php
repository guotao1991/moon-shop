<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Admin\Role\AddRoleRequest;
use App\Http\Resources\V1\Admin\RoleResource;
use App\Services\V1\RoleService;
use Exception;

class RoleController extends Controller
{
    protected $roleService;

    /**
     * UserController constructor.
     * @param RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * 添加角色
     *
     * @param AddRoleRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function add(AddRoleRequest $request)
    {
        $data = $request->validated();
        $res = $this->roleService->addRole($data);

        if (!$res) {
            return $this->failed("添加失败");
        }

        return $this->success();
    }

    /**
     * 获取角色列表
     *
     * @return array|mixed
     * @throws Exception
     */
    public function roleList()
    {
        $list = $this->roleService->getRoleList();
        return $this->success(RoleResource::collection($list));
    }

    /**
     * 获取角色详情
     *
     * @param int $roleId
     * @return array|mixed
     * @throws Exception
     */
    public function roleInfo(int $roleId)
    {
        $info = $this->roleService->getRoleInfoById($roleId);
        return $this->success(new RoleResource($info));
    }
}
