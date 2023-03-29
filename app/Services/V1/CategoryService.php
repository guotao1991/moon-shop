<?php

namespace App\Services\V1;

use App\Exceptions\DbErrorException;
use App\Exceptions\NotesException;
use App\Models\Goods\CategoryAttributeModel;
use App\Models\Goods\CategoryModel;
use App\Models\Goods\ColorsModel;
use App\Repositories\V1\CategoryRepository;
use App\Utils\Helper;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use phpDocumentor\Reflection\Types\Integer;
use Throwable;

/**
 * Class CategoryService
 * @package App\Services\V1
 *
 */
class CategoryService extends BaseService
{

    protected $catRepo;

    /**
     * CategoryService constructor.
     * @param CategoryRepository $catRepo
     */
    public function __construct(CategoryRepository $catRepo)
    {
        $this->catRepo = $catRepo;
    }

    /**
     * 添加商品分类
     *
     * @param array $data
     * @return CategoryModel
     * @throws Throwable
     */
    public function addCat(array $data): CategoryModel
    {
        $parentId = $data['parent_id'] ?? 0;
        $catName = $data['cat_name'];
        $code = "0";
        $admin = Helper::admin();
        if ($parentId > 0) {
            //如果有父分类，获取父分类信息
            try {
                $parentCat = $this->catRepo->getCatInfoById($parentId);
                if ($parentCat->hq_id != $admin->getHq()->id) {
                    throw new NotesException("父分类不存在");
                }
                $code = $parentCat->code . "-" . $parentCat->id;
            } catch (NotFound $nf) {
                throw new NotesException("父分类不存在");
            }
        }

        try {
            $this->catRepo->getCatByName($admin->getHq()->id, $catName);
            throw new NotesException("分类名已存在");
        } catch (NotFound $nf) {
        }

        return $this->catRepo->addCat($admin->last_hq_id, $catName, $parentId, $code);
    }

    /**
     * 编辑商品分类
     *
     * @param array $data
     * @return CategoryModel
     * @throws Throwable
     */
    public function editCat(array $data): CategoryModel
    {
        $catId = $data['cat_id'];
        $parentId = $data['parent_id'] ?? 0;
        $catName = $data['cat_name'];
        $code = "0";
        $admin = Helper::admin();
        //如果有父分类，获取父分类信息
        $catInfo = $this->catRepo->getCatInfoById($catId);
        if ($catInfo->hq_id != $admin->getHq()->id) {
            throw new NotesException("分类不存在");
        }

        if ($catId == $parentId) {
            throw new NotesException("父分类不能是自己");
        }

        if ($parentId > 0) {
            //如果有父分类，获取父分类信息
            try {
                $parentCat = $this->catRepo->getCatInfoById($parentId);
                if ($parentCat->hq_id != $admin->getHq()->id) {
                    throw new NotesException("父分类不存在");
                }
                $code = $parentCat->code . "-" . $parentCat->id;
            } catch (NotFound $nf) {
                throw new NotesException("父分类不存在");
            }
        }

        try {
            $nameInfo = $this->catRepo->getCatByName($admin->getHq()->id, $catName);
            if ($nameInfo->id != $catId) {
                throw new NotesException("分类名已存在");
            }
        } catch (NotFound $nf) {
        }

        $catInfo->parent_id = $parentId;
        $catInfo->name = $catName;
        $catInfo->code = $code;
        $res = $catInfo->save();

        if ($res === false) {
            throw new DbErrorException("修改规格表失败");
        }

        return $catInfo;
    }

    /**
     * 获取分类列表
     * @throws Exception
     * @return CategoryModel[]
     */
    public function catList()
    {
        $admin = Helper::admin();
        return $this->catRepo->getCatList($admin);
    }

    /**
     * 获取店铺商品的颜色选择列表
     *
     * @return ColorsModel[]
     * @throws Exception
     */
    public function colorList()
    {
        $admin = Helper::admin();
        return $this->catRepo->getColorList($admin);
    }

    /**
     * 获取类目详情
     *
     * @param int $catId 类目ID
     * @return CategoryModel
     * @throws NotFound
     * @throws Exception
     */
    public function getCatInfoById(int $catId)
    {
        $admin = Helper::admin();
        $catInfo = $this->catRepo->getCatInfoById($catId);

        if ($catInfo->hq_id != $admin->last_hq_id) {
            throw new NotFound("类目没有找到");
        }

        return $catInfo;
    }

    /**
     * 添加颜色数据
     *
     * @param string $colorName 颜色名字
     * @return ColorsModel
     * @throws DbErrorException
     * @throws Exception
     */
    public function addColor(string $colorName)
    {
        $admin = Helper::admin();
        try {
            $this->catRepo->getColorByName($admin->last_hq_id, $colorName);
            throw new NotesException("颜色数据错误");
        } catch (NotFound $e) {
        }
        return $this->catRepo->addColor($admin->last_hq_id, $colorName);
    }

    /**
     * 根据Id查询颜色数据
     * @param int $colorId 颜色ID
     * @return mixed
     * @throws NotFound
     * @throws Exception
     */
    public function getColorInfoById(int $colorId)
    {
        $admin = Helper::admin();
        $colorInfo = $this->catRepo->getColorInfoById($colorId);

        if ($colorInfo->hq_id != $admin->last_hq_id) {
            throw new NotFound("类目没有找到");
        }

        return $colorInfo;
    }

    /**
     * 添加类目规格
     *
     * @param int $catId 类目ID
     * @param string $attrName 规格名称
     * @return CategoryAttributeModel
     * @throws DbErrorException
     * @throws NotesException
     */
    public function addAttr(int $catId, string $attrName)
    {
        return $this->catRepo->addCatAttr($catId, $attrName);
    }

    /**
     * @param $attrId
     * @return CategoryAttributeModel
     * @throws NotFound
     */
    public function getAttrById($attrId)
    {
        return $this->catRepo->getAttrInfoById($attrId);
    }
}
