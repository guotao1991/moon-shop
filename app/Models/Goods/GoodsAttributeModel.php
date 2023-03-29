<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Goods;

use App\Exceptions\DbErrorException;
use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class GoodsAttributeModel
 *
 * @property int $id
 * @property int $goods_id
 * @property int $attr_id
 * @property int $color_id
 * @property float $goods_price
 * @property int $stock_num
 * @property int $sale_num
 * @property string $bar_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property CategoryAttributeModel $attr
 * @property ColorsModel $color
 *
 * @package App\Models\Goods
 */
class GoodsAttributeModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'goods_attribute';

    protected $casts = [
        'goods_id' => 'int',
        'goods_price' => 'float',
        'stock_num' => 'int',
        'sale_num' => 'int',
        'color_id' => 'int',
        'attr_id' => 'int',
    ];

    protected $fillable = [
        'goods_id',
        'attr_id',
        'color_id',
        'goods_price',
        'stock_num',
        'sale_num',
        'bar_code'
    ];

    /**
     * 生成商品唯一码
     * @throws DbErrorException
     */
    public function makeBarCode()
    {
        $this->bar_code = "G" . str_pad(dechex($this->id), 15, "0");
        $res = $this->save();

        if ($res === false) {
            throw new DbErrorException("生成商品唯一码失败");
        }
    }

    /**
     * 关联规格模型
     * @return HasOne
     */
    public function attr()
    {
        return $this->hasOne(CategoryAttributeModel::class, "id", "attr_id");
    }

    /**
     * 关联颜色模型
     * @return HasOne
     */
    public function color()
    {
        return $this->hasOne(ColorsModel::class, "id", "color_id");
    }
}
