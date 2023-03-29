<?php

namespace App\Listeners\Goods\Edit;

use App\Events\Goods\GoodsEditEvent;
use App\Models\Goods\ColorsModel;
use App\Services\V1\CategoryService;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * 商品添加事件
 *
 * Class ColorsListener
 * @package App\Listeners\Goods\Add
 */
class ColorsListener
{
    protected $catService;
    protected $addColorData;

    public function __construct(CategoryService $catService)
    {
        $this->catService = $catService;
    }

    /**
     * @param GoodsEditEvent $event
     * @throws Throwable
     */
    public function handle(GoodsEditEvent $event)
    {
        Log::info("编辑商品，执行颜色处理事件");
        $newColors = $this->addColor($event->colors);
        $newAttrColorList = $this->processingData($event->attrColorList);

        $event->colors = $newColors;
        $event->attrColorList = $newAttrColorList;
    }

    /**
     * 处理提交的颜色数据
     *
     * @param array $colors
     * @return ColorsModel[]
     * @throws Throwable
     */
    private function addColor(array $colors)
    {
        $newColors = [];
        foreach ($colors as $color) {
            if ($color['is_new']) {
                $info = $this->catService->addColor($color['color_name']);
                $this->addColorData[$color['color_id']] = $info->id;

                $newColors[] = $info;
            } else {
                $newColors[] = $this->catService->getColorInfoById($color['color_id']);
            }
        }

        return $newColors;
    }

    /**
     * 处理新增的规格数据
     *
     * @param array $attrColorList
     * @return array
     */
    private function processingData(array $attrColorList)
    {
        foreach ($attrColorList as $index => $item) {
            if (!empty($this->addColorData[$item['color_id']])) {
                $attrColorList[$index]['color_id'] = $this->addColorData[$item['color_id']];
            }
        }

        return $attrColorList;
    }
}
