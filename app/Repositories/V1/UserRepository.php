<?php

namespace App\Repositories\V1;

use App\Exceptions\DbErrorException;
use App\Models\Admin\AdminModel;
use App\Models\User\UserModel;
use App\Models\User\UserOrderTagModel;
use App\Models\User\UserTagModel;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class UserRepository extends BaseRepository
{
    /**
     * 根据手机号获取用户信息
     * @param string $mobile 手机号
     * @return UserModel
     * @throws Exception
     */
    public function getUserByMobile(string $mobile): UserModel
    {
        $user = UserModel::withoutTrashed()
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
        $count = UserModel::withTrashed()
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
     * 改用户信息
     * @param UserModel $user 用户信息
     * @param array $data 用户信息
     * @return bool
     * @throws Exception
     */
    public function updateUserInfo(UserModel $user, array $data): bool
    {
        if (!empty($data['nick_name'])) {
            $user->nick_name = $data['nick_name'];
        }

        if (!empty($data['head_img'])) {
            $user->head_img = $data['head_img'];
        }

        if (!empty($data['mobile'])) {
            $user->mobile = $data['mobile'];
        }

        if (!empty($data['sex'])) {
            $user->sex = $data['sex'];
        }

        if (!empty($data['birthday'])) {
            $user->birthday = $data['birthday'];
        }

        $res = $user->save();

        if (!$res) {
            throw new DbErrorException("修改用户信息失败");
        }
        return true;
    }

    /**
     * 根据token获取用户信息
     *
     * @param string $token
     * @return UserModel
     * @throws NotFound
     */
    public function getUserByToken(string $token): UserModel
    {
        $info = UserModel::withoutTrashed()
            ->whereToken($token)
            ->first();

        if (empty($info)) {
            throw new NotFound("没有找到用户");
        }

        return $info;
    }

    /**
     * 增加客人
     *
     * @param array $data
     * @return UserModel
     * @throws Exception
     */
    public function addClient(array $data): UserModel
    {
        $user = new UserModel();
        $nowDate = date("Y-m-d H:i:s");
        $user->nick_name = $data['nick_name'] ?? "用户" . uniqid();
        $user->token = $this->generateToken();
        $user->last_login_time = $nowDate;
        $user->token_time = $nowDate;
        $user->mobile = $data['mobile'] ?? "";
        $user->sex = (int)($data['sex'] ?? 0);
        if (!empty($data['birthday'])) {
            $user->birthday = $data['birthday'];
        }
        $user->wx_openid = $data['openid'] ?? "";
        $user->wx_unionid = $data['unionid'] ?? "";

        $res = $user->save();

        if ($res === false) {
            throw new Exception("注册失败，请联系管理员");
        }
        $user->refresh();

        return $user;
    }

    /**
     * 获取用户列表
     *
     * @param AdminModel $admin 管理员信息
     * @param array $data
     * @return array
     * @throws Throwable
     */
    public function getUserList(AdminModel $admin, array $data)
    {
        $search = $data['search'] ?? "";
        $tagNames = json_decode($data['tag_names'] ?? "[]", 1);
        $page = (int)($data['page'] ?? 1);
        $pageSize = (int)($data['page_size'] ?? 10);
        $sort = (int)$data['sort'] ?? 1;
        $minConsumption = (float)$data["min_consumption"] ?? "";
        $maxConsumption = (float)$data["max_consumption"] ?? "";
        $minDay = (int)$data["min_day"] ?? "";
        $maxDay = (int)$data["max_day"] ?? "";

        $query = UserModel::whereHqId($admin->hq_id)->with(['level']);

        if (!empty($tagNames)) {
            $query->whereHas('tags', function ($sql) use ($tagNames) {
                    /** @var Builder $sql */
                    $sql->whereIn("tag_name", $tagNames);
            });
        }

        if (!empty($search)) {
            $query->where("nick_name", "like", "{$search}%")
                ->orWhere("mobile", "like", "{$search}%");
        }

        //根据最低消费查询
        if (!empty($minConsumption)) {
            $query->where("total_pay_amount", ">=", $minConsumption);
        }

        //根据最高消费查询
        if (!empty($maxConsumption)) {
            $query->where("total_pay_amount", "<", $maxConsumption);
        }

        //根据最低消费天数
        if (!empty($minDay)) {
            $minSec = $minDay * 24 * 60 * 60;
            $query->whereRaw(time() . " - UNIX_TIMESTAMP(last_consumption_time) >= {$minSec}");
        }

        //根据最高消费天数
        if (!empty($maxDay)) {
            $maxSec = $maxDay * 24 * 60 * 60;
            $query->whereRaw(time() . " - UNIX_TIMESTAMP(last_consumption_time) < {$maxSec}");
        }

        if ($sort == 2) {
            $query->orderBy("total_profit_amount");
        } else {
            $query->orderBy("total_pay_amount");
        }

        $start = ($page - 1) * $pageSize;

        return $query->orderByDesc("attention")
            ->orderByDesc("created_at")
            ->offset($start)
            ->limit($pageSize)
            ->get();
    }

    /**
     * 根据用户Id获取用户信息
     *
     * @param AdminModel $admin
     * @param int $userId 用户ID
     * @return UserModel
     * @throws NotFound
     */
    public function getHqUserById(AdminModel $admin, int $userId): UserModel
    {
        $info = UserModel::whereId($userId)
            ->where("hq_id", $admin->last_hq_id)
            ->first();

        if (empty($info)) {
            throw new NotFound("用户不存在");
        }

        return $info;
    }

    /**
     * 修改用户标签
     *
     * @param int $userId 用户ID
     * @param array $tags 标签数组
     * @return bool
     * @throws Exception
     */
    public function addUserTag(int $userId, array $tags)
    {
        $res = (new UserTagModel())->newModelQuery()
            ->where("user_id", $userId)
            ->delete();

        if ($res === false) {
            throw new Exception("用户标签操作失败");
        }

        //增加用户TAG
        foreach ($tags as $tag) {
            //插入关联表
            $userTag = new UserTagModel();
            $userTag->user_id = $userId;
            $userTag->tag_name = $tag;

            $res = $userTag->save();

            if ($res === false) {
                throw new Exception("插入用户标签关联表失败");
            }
        }

        return true;
    }

    /**
     * 修改用户信息
     *
     * @param UserModel $userInfo
     * @param array $data
     * @return UserModel
     * @throws DbErrorException
     */
    public function editUser(UserModel $userInfo, array $data): UserModel
    {
        $userInfo->nick_name = $data["nick_name"];
        $userInfo->head_img = $data["head_img"] ?? "";
        $userInfo->sex = (int)$data['sex'] ?? 0;
        $userInfo->birthday = $data['birthday'] ?? null;
        $userInfo->remark = $data['remark'] ?? "";

        if (!empty($data["mobile"])) {
            $userInfo->mobile = $data["mobile"];
        }

        if (!empty($data['password'])) {
            $userInfo->password = MD5($data["password"]);
        }

        $res = $userInfo->save();

        if ($res === false) {
            throw new DbErrorException("修改用户信息失败");
        }

        return $userInfo;
    }

    /**
     * 删除用户所有标签
     *
     * @param int $userId 用户ID
     * @return bool
     * @throws Exception
     */
    public function delTags(int $userId): bool
    {
        $res = UserTagModel::whereUserId($userId)->delete();

        if ($res === false) {
            throw new DbErrorException("用户标签删除失败");
        }

        return true;
    }

    /**
     * 获取用户tag
     * @param AdminModel $admin
     * @param array $userIds
     * @return UserTagModel[]
     * @throws Exception
     */
    public function getUserTagByUserIds(AdminModel $admin, array $userIds)
    {
        $list = UserTagModel::whereHqId($admin->getHq()->id)
            ->with("tag")
            ->whereIn("user_id", $userIds)
            ->get();

        if (count($list) > 0) {
            return $list;
        }

        return [];
    }

    /**
     * 获取用户下单的商品标签，根据最多的排序
     *
     * @param int $userId
     * @param int $num
     * @return UserOrderTagModel[]|Builder[]|Collection
     */
    public function getUserOrderTags(int $userId, int $num)
    {
        return UserOrderTagModel::whereUserId($userId)
            ->orderByDesc("num")
            ->limit($num)
            ->get();
    }

    /**
     * 验证用户手机号是否存在
     * @param int $userId
     * @param string $mobile
     * @return bool
     */
    public function checkMobileUse(int $userId, string $mobile): bool
    {
        return UserModel::withoutTrashed()
            ->where("mobile", $mobile)
            ->where("id", "!=", $userId)
            ->exists();
    }

    /**
     * @param AdminModel $admin
     * @param int $userId
     * @param $tag
     * @return UserTagModel|Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object
     * @throws NotFound
     */
    public function getUserTagByName(AdminModel $admin, int $userId, $tag)
    {
        $info = UserTagModel::withoutTrashed()
            ->where("user_id", $userId)
            ->where("tag_name", $tag)
            ->where("hq_id", $admin->last_hq_id)
            ->first();

        if (empty($info)) {
            throw new NotFound("没有找到用户标签");
        }

        return $info;
    }

    /**
     * 清理用户多余的标签
     * @param AdminModel $admin
     * @param int $userId
     * @param array $tags
     * @return bool
     * @throws DbErrorException
     */
    public function clearUserTag(AdminModel $admin, int $userId, array $tags = []): bool
    {
        $res = UserTagModel::withoutTrashed()
            ->where("user_id", $userId)
            ->whereNotIn("tag_name", $tags)
            ->where("hq_id", $admin->last_hq_id)
            ->first();

        if ($res === false) {
            throw new DbErrorException("清理用户标签失败");
        }

        return true;
    }
}
