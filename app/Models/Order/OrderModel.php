<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Order;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrderModel
 *
 * @property int $id
 * @property string $order_no
 * @property int $store_id
 * @property float $order_amount
 * @property int $hq_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Order
 */
class OrderModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'order';

    protected $casts = [
        'store_id' => 'int',
        'order_amount' => 'float',
        'hq_id' => 'int'
    ];

    protected $fillable = [
        'order_no',
        'store_id',
        'order_amount',
        'hq_id'
    ];
}
