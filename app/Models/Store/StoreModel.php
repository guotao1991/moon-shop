<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Store;

use App\Models\BaseModel;
use App\Models\Common\TagModel;
use App\Models\Goods\GoodsModel;
use App\Models\Goods\GoodsStoreModel;
use App\Models\User\UserTagModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class StoreModel
 *
 * @property int $id
 * @property string $store_name
 * @property int $province_id
 * @property int $city_id
 * @property int $district_id
 * @property string $address
 * @property string $lgt
 * @property string $lat
 * @property string $head_img
 * @property int $status
 * @property string $contact_name
 * @property string $mobile
 * @property int $hq_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property UserTagModel[] $tags
 * @property HqModel $hq
 * @property GoodsModel[] $goodsList
 *
 * @method static \Illuminate\Database\Eloquent\Builder|StoreModel where($column, $operator, $value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreModel whereId($value)
 *
 * @package App\Models\Store
 */
class StoreModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'store';

    protected $casts = [
        'admin_id' => 'int',
        'province_id' => 'int',
        'city_id' => 'int',
        'district_id' => 'int',
        'status' => 'int',
        'hq_id' => 'int'
    ];

    protected $fillable = [
        'store_name',
        'admin_id',
        'province_id',
        'city_id',
        'district_id',
        'address',
        'lgt',
        'lat',
        'head_img',
        'status',
        'contact_name',
        'mobile',
        'hq_id'
    ];

    /** @var int 营业中 */
    public const STATUS_OPEN = 1;
    /** @var int 停止营业 */
    public const STATUS_CLOSE = 2;

    /**
     * 关联标签模型
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags()
    {
        return $this->hasMany(UserTagModel::class, "store_id", "id");
    }

    /**
     * 关联HQ模型
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hq()
    {
        return $this->hasOne(HqModel::class, "id", "hq_id");
    }

    /**
     * 店铺所有商品
     * @return BelongsToMany
     */
    public function goodsList()
    {
        return $this->belongsToMany(GoodsModel::class, "goods_store", "store_id", "goods_id")
            ->using(GoodsStoreModel::class)
            ->withPivot(
                [
                    'created_at',
                    'updated_at',
                ]
            )->withTimestamps();
    }
}
