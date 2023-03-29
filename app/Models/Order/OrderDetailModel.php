<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Order;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrderDetailModel
 *
 * @property int $id
 * @property string $order_no
 * @property int $goods_id
 * @property int $goods_mirror_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Order
 */
class OrderDetailModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'order_detail';

    protected $casts = [
        'goods_id' => 'int',
        'goods_mirror_id' => 'int'
    ];

    protected $fillable = [
        'order_no',
        'goods_id',
        'goods_mirror_id'
    ];
}
