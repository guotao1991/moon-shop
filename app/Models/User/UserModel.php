<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\User;

use App\Models\Admin\AdminModel;
use App\Models\BaseModel;
use App\Models\Store\HqModel;
use App\Models\Store\StoreUserModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserModel
 *
 * @property int $id
 * @property string $nick_name
 * @property string $mobile
 * @property string $head_img
 * @property int $sex
 * @property Carbon|null $birthday
 * @property string $province_name
 * @property string $city_name
 * @property string $district_name
 * @property string $wx_openid
 * @property string $wx_unionid
 * @property string $token
 * @property Carbon $token_time
 * @property Carbon $last_login_time
 * @property int $login_type
 * @property int $login_hq_id
 * @property int $login_store_id
 * @property int $is_authorized
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property AdminModel|Collection $admin
 * @property StoreUserModel[]|Collection $storeUsers
 * @property StoreUserModel $storeUser
 * @property StoreUserModel $hqUser
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserModel whereToken($value)
 *
 * @package App\Models\User
 */
class UserModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'user';

    protected $casts = [
        'sex' => 'int',
        'login_type' => 'int',
        'login_hq_id' => 'int',
        'login_store_id' => 'int'
    ];

    protected $dates = [
        'birthday',
        'token_time',
        'last_login_time'
    ];

    protected $hidden = [
        'token'
    ];

    protected $fillable = [
        'nick_name',
        'mobile',
        'head_img',
        'sex',
        'birthday',
        'province_name',
        'city_name',
        'district_name',
        'wx_openid',
        'wx_unionid',
        'token',
        'token_time',
        'last_login_time',
        'login_type',
        'login_hq_id',
        'login_store_id'
    ];

    /** @var int 登录类型：用户登录 */
    public const LOGIN_TYPE_USER = 1;
    /** @var int 登录类型：管理员登录 */
    public const LOGIN_TYPE_ADMIN = 2;

    /** @var int 未授权注册商家 */
    public const AUTHORIZED_FALSE = 1;
    /** @var int 已授权注册商家 */
    public const AUTHORIZED_TRUE = 2;


    /**
     * 关联管理员模型
     * @return hasOne
     */
    public function admin()
    {
        return $this->hasOne(AdminModel::class, "user_id", "id");
    }

    /**
     * 关联店铺用户模型
     * @return HasMany
     */
    public function storeUsers()
    {
        return $this->hasMany(StoreUserModel::class, "user_id", "id");
    }

    /**
     * 获取用户最后登录的店铺
     * @return HasOne
     */
    public function storeUser()
    {
        return $this->hasOne(StoreUserModel::class, "store_id", "login_store_id")
            ->where("login_type", UserModel::LOGIN_TYPE_USER);
    }

    /**
     * 获取用户最后登录的HQ
     * @return HasOne
     */
    public function hqUser()
    {
        return $this->hasOne(StoreUserModel::class, "hq_id", "login_hq_id")
            ->where("login_type", UserModel::LOGIN_TYPE_USER);
    }

    /**
     * 获取用户最后登录的HQ
     * @return HasOne
     */
    public function adminHq()
    {
        return $this->hasOne(HqModel::class, "id", "login_hq_id")
            ->where("login_type", UserModel::LOGIN_TYPE_ADMIN);
    }

    /**
     * 生成用户TOKEN
     * @throws Exception
     */
    public function generateToken(): void
    {
        $token = MD5(time() . uniqid());
        if ($this->tokenIsUsed($token)) {
            $this->generateToken();
        }
        $this->token = $token;
    }


    /**
     * 根据token获取用户信息
     * @param string $token
     * @return bool
     * @throws Exception
     */
    public function tokenIsUsed(string $token): bool
    {
        $count = UserModel::withoutTrashed()
            ->where("token", $token)
            ->count();

        if ($count > 0) {
            return true;
        }
        return false;
    }
}
