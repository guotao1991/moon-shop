<?php

namespace App\Repositories\V1;

use App\Exceptions\DbErrorException;
use App\Exceptions\NotesException;
use App\Models\Admin\AdminModel;
use App\Models\Goods\CategoryAttributeModel;
use App\Models\Goods\CategoryModel;
use App\Models\Goods\ColorsModel;
use Facade\FlareClient\Http\Exceptions\NotFound;

class CategoryRepository extends BaseRepository
{
    /**
     * 根据ID获取分类
     *
     * @param int $catId
     * @return CategoryModel
     * @throws NotFound
     */
    public function getCatInfoById(int $catId): CategoryModel
    {
        $info = CategoryModel::withoutTrashed()
            ->where("id", $catId)
            ->first();

        if (empty($info)) {
            throw new NotFound("分类不存在");
        }

        return $info;
    }

    /**
     * @param int $hqId
     * @param string $catName
     * @return CategoryModel
     * @throws NotFound
     */
    public function getCatByName(int $hqId, string $catName): CategoryModel
    {
        $info = CategoryModel::withoutTrashed()
            ->where("name", $catName)
            ->where("hq_id", $hqId)
            ->first();

        if (empty($info)) {
            throw new NotFound("分类不存在");
        }

        return $info;
    }

    /**
     * 插入分类表
     *
     * @param int $hqId HQ ID
     * @param string $catName 分类名
     * @param int $parentId 父分类ID
     * @param string $code
     * @return CategoryModel
     * @throws DbErrorException
     */
    public function addCat(int $hqId, string $catName, int $parentId = 0, string $code = ""): CategoryModel
    {
        $catModel = new CategoryModel();
        $catModel->name = $catName;
        $catModel->hq_id = $hqId;
        $catModel->parent_id = $parentId;
        $catModel->code = $code;
        $res = $catModel->save();

        if (!$res) {
            throw new DbErrorException("插入分类表失败");
        }

        $catModel->refresh();
        return $catModel;
    }

    /**
     * 获取分类列表
     * @param AdminModel $admin
     * @return CategoryModel[]
     */
    public function getCatList(AdminModel $admin)
    {
        return CategoryModel::withoutTrashed()
            ->with(["categoryAttribute"])
            ->where("hq_id", $admin->last_hq_id)
            ->get();
    }

    /**
     * 获取店铺商品的颜色选择列表
     *
     * @param AdminModel $admin
     * @return ColorsModel[]
     */
    public function getColorList(AdminModel $admin)
    {
        return ColorsModel::withoutTrashed()
            ->where("hq_id", $admin->last_hq_id)
            ->get();
    }

    /**
     * 新增商品类目属性
     * @param int $catId 类目ID
     * @param string $attrName 属性名称
     * @return CategoryAttributeModel
     * @throws DbErrorException
     * @throws NotesException
     */
    public function addCatAttr(int $catId, string $attrName)
    {
        if (empty($attrName)) {
            throw new NotesException("属性名称不能为空");
        }
        $catAttr = new CategoryAttributeModel();
        $catAttr->category_id = $catId;
        $catAttr->attribute_value = $attrName;
        $res = $catAttr->save();

        if (!$res) {
            throw new DbErrorException("记录属性失败");
        }

        $catAttr->refresh();
        return $catAttr;
    }

    /**
     * 插入分类表
     *
     * @param int $hqId HQ ID
     * @param string $colorName 分类名
     * @return ColorsModel
     * @throws DbErrorException
     */
    public function addColor(int $hqId, string $colorName): ColorsModel
    {
        $colorModel = new ColorsModel();
        $colorModel->color_name = $colorName;
        $colorModel->hq_id = $hqId;
        $res = $colorModel->save();

        if (!$res) {
            throw new DbErrorException("插入颜色表失败");
        }

        $colorModel->refresh();
        return $colorModel;
    }

    /**
     * @param int $hqId
     * @param string $colorName
     * @return ColorsModel
     * @throws NotFound
     */
    public function getColorByName(int $hqId, string $colorName): ColorsModel
    {
        $info = ColorsModel::withoutTrashed()
            ->where("color_name", $colorName)
            ->where("hq_id", $hqId)
            ->first();

        if (empty($info)) {
            throw new NotFound("颜色不存在");
        }

        return $info;
    }

    /**
     * 根据ID获取颜色详情
     * @param int $colorId
     * @return ColorsModel
     * @throws NotFound
     */
    public function getColorInfoById(int $colorId): ColorsModel
    {
        $info = ColorsModel::withoutTrashed()
            ->where("id", $colorId)
            ->first();

        if (empty($info)) {
            throw new NotFound("颜色不存在");
        }

        return $info;
    }

    /**
     * 根据规格ID获取规格信息
     * @param $attrId
     * @return CategoryAttributeModel
     * @throws NotFound
     */
    public function getAttrInfoById(int $attrId): CategoryAttributeModel
    {
        $info = CategoryAttributeModel::withoutTrashed()
            ->where("id", $attrId)
            ->first();

        if (empty($info)) {
            throw new NotFound("规格不存在");
        }

        return $info;
    }
}
