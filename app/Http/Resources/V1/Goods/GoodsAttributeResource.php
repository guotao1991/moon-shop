<?php

namespace App\Http\Resources\V1\Goods;

use App\Http\Resources\V1\BaseResources;
use App\Models\Goods\CategoryAttributeModel;
use App\Models\Goods\ColorsModel;
use App\Utils\Helper;
use Illuminate\Http\Request;

/**
 * Class GoodsAttributeResource
 * @package App\Http\Resources\V1\Goods
 *
 * @property int $goods_id
 * @property int $attr_id
 * @property int $color_id
 * @property int $goods_price
 * @property int $stock_num
 * @property int $sale_num
 *
 * @property CategoryAttributeModel $attr
 * @property ColorsModel $color
 */
class GoodsAttributeResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "goods_id" => $this->goods_id,
            "attr" => new CategoryAttributeResource($this->attr),
            "color" => new ColorsResource($this->color),
            "goods_price" => Helper::formatPrice($this->goods_price),
            "stock_num" => $this->stock_num,
            "sale_num" => $this->sale_num
        ];
    }
}
