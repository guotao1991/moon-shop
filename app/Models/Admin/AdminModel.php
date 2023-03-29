<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Admin;

use App\Exceptions\DeletedException;
use App\Exceptions\NotesException;
use App\Models\BaseModel;
use App\Models\Store\HqModel;
use App\Models\Store\StoreModel;
use App\Models\User\UserModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AdminModel
 *
 * @property int $id
 * @property int $user_id
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property UserModel|Collection $user
 * @property HqModel[]|Collection $hqList
 * @property StoreModel[]|Collection $storeList
 *
 * @package App\Models\Admin
 */
class AdminModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'admin';

    protected $casts = [
        'user_id' => 'int',
        'status' => 'int'
    ];

    protected $dates = [];

    protected $fillable = [
        'user_id',
        'status'
    ];

    /** @var int 账号正常 */
    public const STATUS_NORMAL = 1;
    /** @var int 账号封禁 */
    public const STATUS_LOCKED = 2;

    /**
     * 监测账号状态
     * @throws Exception
     */
    public function checkStatus()
    {
        if ($this->status == self::STATUS_LOCKED) {
            throw new NotesException("账号被禁用");
        }

        if (!empty($this->deleted_at)) {
            throw new DeletedException("账号已被禁用");
        }
    }

    /**
     * 关联用户模型
     *
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(UserModel::class, "id", "user_id");
    }

    /**
     * 关联HQ模型
     * @return BelongsToMany
     */
    public function hqList()
    {
        return $this->belongsToMany(HqModel::class, AdminHqModel::class, "admin_id", "hq_id");
    }

    /**
     * 关联店铺模型
     * @return BelongsToMany
     */
    public function storeList()
    {
        return $this->belongsToMany(StoreModel::class, AdminStoreModel::class, "admin_id", "store_id");
    }
}
