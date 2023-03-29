<?php

namespace App\Services\V1;

use App\Exceptions\NotesException;
use App\Models\Admin\RoleModel;
use App\Repositories\V1\RoleRepository;
use App\Utils\Helper;
use Exception;
use Illuminate\Support\Facades\DB;

class RoleService extends BaseService
{
    protected $roleRepo;

    /**
     * RoleService constructor.
     *
     * @param RoleRepository $roleRepo
     */
    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    /**
     * 添加角色
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function addRole(array $data): bool
    {
        $roleName = $data['role_name'];
        $menuIds = json_decode($data['menu_ids'], 1);
        $admin = Helper::admin();

        $res = $this->roleRepo->roleNameIsUsed($admin->hq_id, $roleName);
        if ($res) {
            throw new NotesException("角色名已存在");
        }
        DB::beginTransaction();
        $role = $this->roleRepo->addRole($admin->hq_id, $roleName);

        foreach ($menuIds as $menuId) {
            //绑定角色和菜单
            $this->roleRepo->addRoleMenu($role->id, $menuId);
        }

        DB::commit();

        return true;
    }

    /**
     * 根据角色ID获取详情
     *
     * @param int $roleId 角色ID
     * @return RoleModel
     * @throws Exception
     */
    public function getRoleInfoById(int $roleId): RoleModel
    {
        $admin = Helper::admin();
        return $this->roleRepo->getRoleInfoById($admin->last_hq_id, $roleId);
    }

    /**
     * 获取角色列表
     *
     * @return RoleModel[]
     * @throws Exception
     */
    public function getRoleList()
    {
        $admin = Helper::admin();
        return $this->roleRepo->getRoleListByHq($admin->last_hq_id);
    }
}
