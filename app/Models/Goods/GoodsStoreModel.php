<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Goods;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class GoodsStoreModel
 *
 * @property int $id
 * @property int $goods_id
 * @property int $store_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsStoreModel whereStoreId($value)
 * @package App\Models\Goods
 */
class GoodsStoreModel extends Pivot
{
    public $incrementing = true;
    protected $table = 'goods_store';

    protected $casts = [
        'goods_id' => 'int',
        'store_id' => 'int'
    ];

    protected $fillable = [
        'goods_id',
        'store_id',
        'created_at',
        'updated_at'
    ];
}
