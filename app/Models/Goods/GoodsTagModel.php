<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Goods;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class GoodsTagModel
 *
 * @property int $id
 * @property int $goods_id
 * @property int $tag_name
 * @property int $hq_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Goods
 */
class GoodsTagModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'goods_tag';

    protected $casts = [
        'goods_id' => 'int',
        'hq_id' => 'int'
    ];

    protected $fillable = [
        'goods_id',
        'tag_name',
        'hq_id'
    ];
}
