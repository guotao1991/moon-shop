<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\User;

use App\Models\BaseModel;

/**
 * Class UserOrderTagModel
 *
 * @property int $id
 * @property int $store_user_id
 * @property string $tag_name
 * @property int $num
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserOrderTagModel whereStoreUserId($value)
 *
 * @package App\Models\User
 */
class UserOrderTagModel extends BaseModel
{
    protected $table = 'user_order_tag';

    protected $casts = [
        'id' => 'int',
        'store_user_id' => 'int',
        'num' => 'int'
    ];

    protected $fillable = [
        'store_user_id',
        'tag_name',
        'num'
    ];
}
