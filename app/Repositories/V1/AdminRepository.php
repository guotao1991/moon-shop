<?php

namespace App\Repositories\V1;

use App\Exceptions\NotesException;
use App\Models\Admin\AdminHqModel;
use App\Models\Admin\AdminModel;
use App\Models\Admin\AdminStoreModel;
use App\Models\System\SmsCodeModel;
use App\Models\User\UserModel;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class AdminRepository extends BaseRepository
{

    /**
     * 添加用户
     * @param array $data 用户信息
     * @return AdminModel
     * @throws Exception
     */
    public function addAdmin(array $data): AdminModel
    {
        $admin = new AdminModel();
        $admin->user_id = $data['user_id'];

        $res = $admin->save();

        if ($res === false) {
            throw new NotesException("注册失败，请联系管理员");
        }
        $admin->refresh();

        return $admin;
    }

    /**
     * 根据手机号获取用户信息
     * @param string $mobile 手机号
     * @return AdminModel
     * @throws NotFound
     */
    public function getAdminByMobile(string $mobile): AdminModel
    {
        $user = AdminModel::withoutTrashed()
            ->where("mobile", $mobile)
            ->first();
        if (empty($user)) {
            throw new NotFound("手机号未注册");
        }
        return $user;
    }

    /**
     * 根据token获取用户信息
     * @param string $token
     * @return bool
     * @throws Exception
     */
    public function tokenIsUsed(string $token): bool
    {
        $count = AdminModel::withoutTrashed()
            ->where("token", $token)
            ->count();

        if ($count > 0) {
            return true;
        }
        return false;
    }

    /**
     * 生成用户TOKEN
     * @return string 用户TOKEN
     * @throws Exception
     */
    public function generateToken(): string
    {
        $token = MD5(time() . uniqid());
        if ($this->tokenIsUsed($token)) {
            return $this->generateToken();
        }
        return $token;
    }


    /**
     * 根据手机号获取短信验证码
     *
     * @param string $mobile 手机号
     * @return SmsCodeModel
     * @throws NotesException
     */
    public function getSmsCodeByMobile(string $mobile): SmsCodeModel
    {
        $smsCodeArr = SmsCodeModel::withoutTrashed()
            ->where("mobile", $mobile)
            ->orderBy("created_at", "DESC")
            ->first();
        if (empty($smsCodeArr)) {
            throw new NotesException("验证码错误");
        }

        return $smsCodeArr;
    }

    /**
     * 根据手机号修改用户信息
     * @param AdminModel $admin 用户信息
     * @param array $data 用户信息
     * @return bool
     * @throws Exception
     */
    public function updateAdminInfo(AdminModel $admin, array $data): bool
    {
        //如果有密码，则需要加密
        if (!empty($data["password"])) {
            $admin->password = MD5($data['password']);
        }

        $res = $admin->save();

        if (!$res) {
            return false;
        }
        return true;
    }

    /**
     * 根据token获取用户信息
     *
     * @param string $token
     * @return AdminModel
     * @throws NotFound
     */
    public function getAdminByToken(string $token): AdminModel
    {
        $info = AdminModel::withoutTrashed()
            ->with("user")
            ->whereHas("user", function ($sql) use ($token) {
                /** @var Builder $sql */
                $sql->where("token", $token);
            })->first();

        if (empty($info)) {
            throw new NotFound("没有找到用户");
        }

        return $info;
    }

    /**
     * 获取管理员是否是店铺管理员
     *
     * @param int $adminId 管理员ID
     * @param int $storeId 店铺ID
     * @return bool true 是店铺管理员， false 不是店铺管理员
     */
    public function getAdminStoreCount(int $adminId, int $storeId): bool
    {
        $count = AdminStoreModel::whereAdminId($adminId)
            ->whereStoreId($storeId)
            ->count();

        if ($count > 0) {
            return true;
        }

        return false;
    }


    /**
     * 获取管理员是否是店铺管理员
     *
     * @param int $adminId 管理员ID
     * @param int $storeId 店铺ID
     * @return AdminStoreModel
     * @throws NotFound
     */
    public function getAdminStore(int $adminId, int $storeId): AdminStoreModel
    {
        $info = AdminStoreModel::whereAdminId($adminId)
            ->whereStoreId($storeId)
            ->first();

        if (empty($info)) {
            throw new NotFound("不是店铺管理员");
        }

        return $info;
    }

    /**
     * 获取管理员是否是HQ管理员
     *
     * @param int $adminId 管理员ID
     * @param int $hqId HQ ID
     * @return bool true 是HQ管理员， false 不是HQ管理员
     */
    public function getAdminHqCount(int $adminId, int $hqId): bool
    {
        $count = AdminHqModel::whereAdminId($adminId)
            ->whereHqId($hqId)
            ->count();

        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * 获取管理员是否是HQ管理员
     *
     * @param int $adminId 管理员ID
     * @param int $hqId HQ ID
     * @return AdminHqModel
     * @throws NotFound
     */
    public function getAdminHq(int $adminId, int $hqId): AdminHqModel
    {
        $info = AdminHqModel::whereAdminId($adminId)
            ->whereHqId($hqId)
            ->first();

        if (empty($info)) {
            throw new NotFound("不是店铺管理员");
        }

        return $info;
    }

    /**
     * 新增管理员
     *
     * @param AdminModel $admin
     * @param array $data
     * @return AdminModel
     * @throws Throwable
     */
    public function addManger(AdminModel $admin, array $data): AdminModel
    {
        try {
            $this->getAdminByMobile($data['mobile']);
            throw new NotesException("手机号已被注册");
        } catch (NotFound $nf) {
        }

        $nowTime = date("Y-m-d H:i:s");
        $manager = new AdminModel();
        $manager->nick_name = "管理员" . uniqid();
        $manager->password = MD5(uniqid());
        $manager->mobile = $data["mobile"];
        $manager->province_name = $admin->province_name;
        $manager->city_name = $admin->city_name;
        $manager->district_name = $admin->district_name;
        $manager->status = AdminModel::STATUS_NORMAL;
        $manager->token = $this->generateToken();
        $manager->token_time = $nowTime;

        $res = $manager->save();

        if (!$res) {
            throw new Exception("添加管理员失败");
        }

        $manager->refresh();
        return $manager;
    }

    /**
     * 关联管理员和店铺
     *
     * @param int $managerId 管理员ID
     * @param int $storeId 店铺ID
     * @param int $roleId 角色ID
     * @return AdminStoreModel
     * @throws Throwable
     */
    public function relationAdminStore(int $managerId, int $storeId, int $roleId): AdminStoreModel
    {
        try {
            $adminStore = $this->getAdminStore($managerId, $storeId);
            $adminStore->role_id = $roleId;
            $res = $adminStore->save();
            if (!$res) {
                throw new Exception("修改管理员关联店铺失败");
            }
            return $adminStore;
        } catch (NotFound $nf) {
        }

        $adminStore = new AdminStoreModel();
        $adminStore->admin_id = $managerId;
        $adminStore->store_id = $storeId;
        $adminStore->role_id = $roleId;

        $res = $adminStore->save();
        if (!$res) {
            throw new Exception("添加管理员关联店铺失败");
        }

        $adminStore->refresh();
        return $adminStore;
    }

    /**
     * @param int $userId 用户ID
     * @return int
     */
    public function getHqCountByUser(int $userId)
    {
        return AdminModel::withoutTrashed()
            ->where("user_id", $userId)
            ->groupBy("hq_id")
            ->count();
    }

    /**
     * @param int $userId
     * @return AdminModel
     * @throws NotFound
     */
    public function getAdminByUserId(int $userId)
    {
        $admin = AdminModel::withoutTrashed()
            ->where("user_id", $userId)
            ->first();

        if (empty($admin)) {
            throw new NotFound("管理员没有找到");
        }

        return $admin;
    }

    /**
     * 用户登录操作
     * @param UserModel $userInfo
     * @return UserModel
     * @throws Exception
     */
    public function login(UserModel $userInfo)
    {
        $userInfo->last_login_time = date("Y-m-d H:i:s");
        $refreshToken = true;
        if ((time() - strtotime($userInfo->token_time)) < (config("auth.token_timeout") * 60)) {
            $refreshToken = false;
        }

        //生成token
        if ($refreshToken) {
            $userInfo->generateToken();
            $userInfo->token_time = date("Y-m-d H:i:s");
        }

        $res = $userInfo->save();
        if (!$res) {
            throw new Exception("登录失败，请检查网络");
        }

        return $userInfo;
    }

    /**
     * 根据union id 获取用户信息
     * @param string $unionId
     * @return UserModel
     * @throws NotFound
     */
    public function getUserByUnionId(string $unionId): UserModel
    {
        $info = UserModel::withoutTrashed()
            ->where("wx_unionid", $unionId)
            ->first();

        if (empty($info)) {
            throw new NotFound("没有找到用户");
        }

        return $info;
    }
}
