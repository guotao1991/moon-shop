<?php

namespace App\Http\Controllers\V1\Goods;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Goods\Category\AddRequest;
use App\Http\Requests\V1\Goods\Category\EditRequest;
use App\Http\Resources\V1\Goods\CategoryResource;
use App\Http\Resources\V1\Goods\ColorsResource;
use App\Services\V1\CategoryService;
use Exception;
use phpDocumentor\Reflection\Types\Collection;
use Throwable;

class CategoryController extends Controller
{
    protected $catService;

    /**
     * CategoryController constructor.
     * @param CategoryService $goodsService
     */
    public function __construct(CategoryService $goodsService)
    {
        $this->catService = $goodsService;
    }

    /**
     * 添加分类
     *
     * @param AddRequest $request
     * @return CategoryResource
     * @throws Throwable
     */
    public function add(AddRequest $request)
    {
        $data = $request->validated();
        $cat = $this->catService->addCat($data);

        return $this->success(new CategoryResource($cat));
    }

    /**
     * 编辑分类
     *
     * @param EditRequest $request
     * @return CategoryResource
     * @throws Throwable
     */
    public function edit(EditRequest $request)
    {
        $data = $request->validated();
        $cat = $this->catService->editCat($data);

        return $this->success(new CategoryResource($cat));
    }

    /**
     * 获取分类列表
     * @throws Exception
     */
    public function list()
    {
        $catList = $this->catService->catList();
        return $this->success(["list" => CategoryResource::collection($catList)]);
    }

    /**
     * 获取颜色列表
     * @throws Exception
     */
    public function colorList()
    {
        $colorList = $this->catService->colorList();

        return $this->success(["list" => ColorsResource::collection($colorList)]);
    }
}
