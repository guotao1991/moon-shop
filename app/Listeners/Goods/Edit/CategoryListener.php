<?php

namespace App\Listeners\Goods\Edit;

use App\Events\Goods\GoodsEditEvent;
use App\Exceptions\DbErrorException;
use App\Exceptions\NotesException;
use App\Models\Goods\CategoryModel;
use App\Services\V1\CategoryService;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * 商品添加事件
 *
 * Class CategoryListener
 * @package App\Listeners\Goods\Add
 */
class CategoryListener
{
    protected $catService;

    /** @var array 记录添加的规格ID和初始化ID，方便替换 */
    protected $addAttrData;

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
        Log::info("编辑商品，执行类目处理事件");
        //处理类目数据
        $category = $event->category;
        $newCat = $this->addCat($category);
        $newAttrColorList = $this->processingData($event->attrColorList);

        $event->category = $newCat;
        $event->attrColorList = $newAttrColorList;
    }

    /**
     * 处理提交的数据
     *
     * @param array $category 规格数据
     * @return CategoryModel|void
     * @throws Throwable
     */
    private function addCat($category)
    {
        $categoryModel = null;
        if ($category['is_new']) {
            $data = [
                "cat_name" => $category['cat_name']
            ];
            $categoryModel = $this->catService->addCat($data);
        } else {
            $categoryModel = $this->catService->getCatInfoById($category['cat_id']);
        }

        //处理规格数据
        $categoryModel->categoryAttribute = $this->addAttr($categoryModel, $category['attr_list']);

        return $categoryModel;
    }

    /**
     * 添加类目规格
     *
     * @param CategoryModel $categoryModel
     * @param array $attrList
     * @return array
     * @throws DbErrorException
     * @throws NotesException
     * @throws NotFound
     */
    private function addAttr(CategoryModel $categoryModel, array $attrList)
    {
        $newAttrList = [];
        foreach ($attrList as $attr) {
            if ($attr['is_new']) {
                $info = $this->catService->addAttr($categoryModel->id, $attr['attr_name']);
                $newAttrList[] = $info;
                $this->addAttrData[$attr['attr_id']] = $info->id;
            } else {
                $info = $this->catService->getAttrById($attr['attr_id']);

                if ($info->category_id != $categoryModel->id) {
                    throw new NotFound("规格数据没有找到");
                }

                $newAttrList[] = $info;
            }
        }

        return $newAttrList;
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
            if (!empty($this->addAttrData[$item['attr_id']])) {
                $attrColorList[$index]['attr_id'] = $this->addAttrData[$item['attr_id']];
            }
        }

        return $attrColorList;
    }
}
