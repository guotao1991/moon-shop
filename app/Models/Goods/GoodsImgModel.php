<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Goods;

use App\Models\BaseModel;
use Carbon\Carbon;

/**
 * Class GoodsImgModel
 *
 * @property int $id
 * @property int $goods_id
 * @property string $img_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsImgModel whereGoodsId($value)
 * @package App\Models\Goods
 */
class GoodsImgModel extends BaseModel
{
    protected $table = 'goods_img';

    protected $casts = [
        'goods_id' => 'int'
    ];

    protected $fillable = [
        'goods_id',
        'img_url'
    ];
}
