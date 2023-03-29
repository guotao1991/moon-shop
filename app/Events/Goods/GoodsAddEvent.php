<?php

namespace App\Events\Goods;

use App\Models\Goods\CategoryModel;
use App\Models\Goods\ColorsModel;
use App\Models\Goods\GoodsAttributeModel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoodsAddEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private $name = 'goods.add';

    /** @var array|CategoryModel 类目数据 */
    public $category;

    /** @var array|ColorsModel 颜色数据 */
    public $colors;

    /** @var array|GoodsAttributeModel 商品规格属性 */
    public $attrColorList;

    /**
     * Create a new event instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->category = is_array($data['cat']) ? $data['cat'] : json_decode($data['cat'] ?? "[]", 1);
        $this->colors = is_array($data['colors']) ? $data['colors'] : json_decode($data['colors'] ?? "[]", 1);
        $this->attrColorList = is_array($data["attr_color_list"]) ? $data["attr_color_list"] : json_decode(
            $data['attr_color_list'] ?? "[]",
            1
        );
    }

    /**
     * 返回数据
     */
    public function getAttrColorList()
    {
        return $this->attrColorList;
    }

    /**
     * 返回数据
     * @return CategoryModel
     */
    public function getCategory()
    {
        return $this->category;
    }
}
