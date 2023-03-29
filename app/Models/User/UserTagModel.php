<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\User;

use App\Models\BaseModel;
use App\Models\Store\StoreUserModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserTagModel
 *
 * @property int $id
 * @property int $store_user_id
 * @property string $tag_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property UserModel $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserTagModel whereStoreUserId($value)
 *
 *
 * @package App\Models\User
 */
class UserTagModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'user_tag';

    protected $casts = [
        'store_user_id' => 'int'
    ];

    protected $fillable = [
        'store_user_id',
        'tag_name'
    ];

    /**
     * 关联店铺用户模型
     * @return HasOne
     */
    public function storeUser()
    {
        return $this->hasOne(StoreUserModel::class, "id", "store_user_id");
    }
}
