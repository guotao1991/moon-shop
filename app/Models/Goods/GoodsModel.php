<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Goods;

use App\Models\BaseModel;
use App\Models\Store\StoreModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class GoodsModel
 *
 * @property int $id
 * @property string $goods_name
 * @property int $cat_id
 * @property int $purchase_price
 * @property int $min_price
 * @property int $max_price
 * @property int $stock_num
 * @property string $cover_img
 * @property string|null $goods_detail
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $hq_id
 *
 * @property GoodsImgModel[] $images
 * @property GoodsTagModel[] $tags
 * @property GoodsStoreModel[] $stores
 * @property GoodsAttributeModel[] $attrList
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsModel whereHqId($value)
 * @package App\Models\Goods
 */
class GoodsModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'goods';

    protected $casts = [
        'cat_id' => 'int',
        'min_price' => 'int',
        'max_price' => 'int',
        'stock_num' => 'int',
        'purchase_price' => 'int',
        'hq_id' => 'int',
    ];

    protected $fillable = [
        'goods_name',
        'cat_id',
        'purchase_price',
        'min_price',
        'max_price',
        'stock_num',
        'cover_img',
        'goods_images',
        'goods_detail',
        'hq_id',
    ];

    /**
     * 关联商品图片模型
     * @return HasMany
     */
    public function images()
    {
        return $this->hasMany(GoodsImgModel::class, "goods_id", "id");
    }

    /**
     * 关联商品标签模型
     * @return hasMany
     */
    public function tags()
    {
        return $this->hasMany(GoodsTagModel::class, "goods_id", "id");
    }

    /**
     * 关联商品关联店铺模型
     * @return BelongsToMany
     */
    public function stores()
    {
        return $this->belongsToMany(StoreModel::class, GoodsStoreModel::class, "goods_id", "store_id");
    }

    /**
     * 关联类别模型
     * @return HasOne
     */
    public function category()
    {
        return $this->hasOne(CategoryModel::class, "id", "cat_id");
    }

    /**
     * 关联商品规格模型
     * @return HasMany
     */
    public function attrList()
    {
        return $this->hasMany(GoodsAttributeModel::class, "goods_id", "id");
    }
}
