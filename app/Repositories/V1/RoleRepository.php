<?php

namespace App\Repositories\V1;

use App\Models\Admin\RoleMenuModel;
use App\Models\Admin\RoleModel;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;

class RoleRepository extends BaseRepository
{
    /**
     * 检查角色名是否重复
     *
     * @param int $hqId HQ ID
     * @param string $roleName 角色名
     * @return bool
     */
    public function roleNameIsUsed(int $hqId, string $roleName): bool
    {
        $count = RoleModel::withoutTrashed()
            ->where("hq_id", $hqId)
            ->where("role_name", $roleName)
            ->count();

        if ($count > 0) {
            return true;
        }
        return false;
    }

    /**
     * 添加角色
     *
     * @param int $hqId HD ID
     * @param string $roleName 角色名称
     * @return RoleModel
     * @throws Exception
     */
    public function addRole(int $hqId, string $roleName): RoleModel
    {
        $role = new RoleModel();
        $role->hq_id = $hqId;
        $role->role_name = $roleName;
        $res = $role->save();

        if ($res === false) {
            throw new Exception("添加角色失败");
        }

        $role->refresh();
        return $role;
    }

    /**
     * 添加菜单和角色绑定关系表
     * @param int $roleId 角色ID
     * @param int $menuId 菜单ID
     * @return RoleMenuModel
     * @throws Exception
     */
    public function addRoleMenu(int $roleId, int $menuId)
    {
        $roleMenu = new RoleMenuModel();
        $roleMenu->role_id = $roleId;
        $roleMenu->menu_id = $menuId;
        $res = $roleMenu->save();

        if ($res === false) {
            throw new Exception("添加角色失败");
        }

        $roleMenu->refresh();
        return $roleMenu;
    }

    /**
     * 根据角色ID获取角色详情
     *
     * @param int $hqId HQ ID
     * @param int $roleId 角色ID
     * @return RoleModel
     * @throws NotFound
     */
    public function getRoleInfoById(int $hqId, int $roleId): RoleModel
    {
        $info = RoleModel::withoutTrashed()
            ->whereId($roleId)
            ->where("hq_id", $hqId)
            ->with(["menus"])
            ->first();

        if (empty($info)) {
            throw new NotFound("没有找到角色");
        }

        return $info;
    }

    /**
     * 获取店铺角色
     *
     * @param int $hqId HQ ID
     * @return RoleModel[]
     */
    public function getRoleListByHq(int $hqId)
    {
        $list = RoleModel::withoutTrashed()
            ->with(["menus"])
            ->where("hq_id", $hqId)
            ->get();

        if (empty($list)) {
            return [];
        }

        return $list;
    }
}
