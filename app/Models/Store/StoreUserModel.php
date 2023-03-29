<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Store;

use App\Models\User\LevelModel;
use App\Models\User\UserModel;
use App\Models\User\UserOrderTagModel;
use App\Models\User\UserTagModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class StoreUserModel
 *
 * @property int $id
 * @property int $hq_id
 * @property int $store_id
 * @property int $user_id
 * @property string|null $remark
 * @property int $total_pay_amount
 * @property int $total_profit_amount
 * @property int $total_reward_points
 * @property int $level_id
 * @property int $attention
 * @property int $current_month_pay_amount
 * @property Carbon|null $last_consumption_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property UserModel $user
 * @property StoreModel $store
 * @property HqModel $hq
 * @property LevelModel $level
 * @property UserTagModel[] $tags
 * @property UserOrderTagModel[] $orderTags
 *
 * @package App\Http\Models
 */
class StoreUserModel extends Model
{
    use SoftDeletes;

    protected $table = 'store_user';

    protected $casts = [
        'hq_id' => 'int',
        'store_id' => 'int',
        'user_id' => 'int',
        'total_pay_amount' => 'int',
        'total_profit_amount' => 'int',
        'total_reward_points' => 'int',
        'level_id' => 'int',
        'attention' => 'int',
        'current_month_pay_amount' => 'int'
    ];

    protected $dates = [
        'last_consumption_time'
    ];

    protected $fillable = [
        'hq_id',
        'store_id',
        'user_id',
        'remark',
        'total_pay_amount',
        'total_profit_amount',
        'total_reward_points',
        'level_id',
        'attention',
        'current_month_pay_amount',
        'last_consumption_time'
    ];

    /**
     * 关联用户模型
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(UserModel::class, "id", "user_id");
    }

    /**
     * 关联店铺模型
     * @return HasOne
     */
    public function store()
    {
        return $this->hasOne(StoreModel::class, "id", "store_id");
    }

    /**
     * 关联HQ模型
     * @return HasOne
     */
    public function hq()
    {
        return $this->hasOne(HqModel::class, "id", "hq_id");
    }

    /**
     * 关联会员等级模型
     * @return HasOne
     */
    public function level()
    {
        return $this->hasOne(LevelModel::class, "id", "level_id");
    }

    /**
     * 关联用户标签模型
     * @return HasMany
     */
    public function tags()
    {
        return $this->hasMany(UserTagModel::class, "store_user_id", "id");
    }

    /**
     * 关联用户订单标签模型
     * @return HasMany
     */
    public function orderTags()
    {
        return $this->hasMany(UserOrderTagModel::class, "store_user_id", "id");
    }
}
