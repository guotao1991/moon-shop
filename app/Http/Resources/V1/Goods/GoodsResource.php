<?php

namespace App\Http\Resources\V1\Goods;

use App\Http\Resources\V1\BaseResources;
use App\Http\Resources\V1\Common\TagResource;
use App\Models\Goods\CategoryModel;
use App\Models\Goods\GoodsAttributeModel;
use App\Models\Goods\GoodsImgModel;
use App\Models\Goods\GoodsStoreModel;
use App\Models\Goods\GoodsTagModel;
use App\Utils\Helper;
use Illuminate\Http\Request;

/**
 * Class GoodsResource
 * @package App\Http\Resources\V1\Goods
 *
 * @property int $id
 * @property string $goods_name
 * @property int $cat_id
 * @property int $min_price
 * @property int $max_price
 * @property int $purchase_price
 * @property int $stock_num
 * @property string $cover_img
 * @property string|null $goods_detail
 *
 * @property CategoryModel $category
 * @property GoodsImgModel[] $images
 * @property GoodsTagModel[] $tags
 * @property GoodsStoreModel[] $stores
 * @property GoodsAttributeModel[] $attrList
 */
class GoodsResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'goods_id' => $this->id,
            'goods_name' => $this->goods_name,
            'category' => new CategoryResource($this->category),
            'min_price' => Helper::formatPrice($this->min_price),
            'max_price' => Helper::formatPrice($this->max_price),
            'purchase_price' => Helper::formatPrice($this->purchase_price),
            'stock_num' => $this->stock_num,
            'cover_img' => $this->cover_img,
            'goods_detail' => $this->goods_detail,
            'images' => GoodsImgResource::collection($this->images),
            'tags' => TagResource::collection($this->tags),
            'attr_list' => GoodsAttributeResource::collection($this->attrList)
        ];
    }
}
