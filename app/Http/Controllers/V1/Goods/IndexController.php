<?php

namespace App\Http\Controllers\V1\Goods;

use App\Events\Goods\GoodsAddEvent;
use App\Events\Goods\GoodsEditEvent;
use App\Exceptions\DbErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Goods\Index\EditRequest;
use App\Http\Requests\V1\Goods\Index\AddRequest;
use App\Http\Requests\V1\Goods\Index\DeleteRequest;
use App\Http\Requests\V1\Goods\Index\EditTagRequest;
use App\Http\Requests\V1\Goods\Index\InfoRequest;
use App\Http\Requests\V1\Goods\Index\ListRequest;
use App\Http\Requests\V1\Goods\Index\SoldOutRequest;
use App\Http\Resources\V1\Goods\GoodsResource;
use App\Services\V1\GoodsService;
use App\Services\V1\TagService;
use Exception;

class IndexController extends Controller
{
    protected $goodsService;

    public function __construct(GoodsService $goodsService)
    {
        $this->goodsService = $goodsService;
    }

    /**
     * 添加商品
     *
     * @param AddRequest $request
     * @return GoodsResource
     * @throws Exception
     */
    public function add(AddRequest $request)
    {
        $data = $request->validated();

        $event = new GoodsAddEvent($data);
        event($event);

        $data['attr_color_list'] = $event->getAttrColorList();
        $data['cat'] = $event->getCategory();

        $goods = $this->goodsService->addGoods($data);

        return $this->success(new GoodsResource($goods));
    }

    /**
     * 商品列表
     * @param ListRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function list(ListRequest $request)
    {
        $data = $request->validated();
        $list = $this->goodsService->list($data);

        return $this->success(["list" => GoodsResource::collection($list)]);
    }

    /**
     * 商品列表
     * @param InfoRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function goodsInfo(InfoRequest $request)
    {
        $data = $request->validated();
        $info = $this->goodsService->info($data);

        return $this->success(new GoodsResource($info));
    }

    /**
     * 获取商品标签
     * @throws Exception
     */
    public function tags()
    {
        /** @var TagService $tagService */
        $tagService = app(TagService::class);

        $list = $tagService->getGoodsTagListByAdmin();
        return $this->success(["list" => $list]);
    }

    /**
     * 删除商品
     *
     * @param DeleteRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function delete(DeleteRequest $request)
    {
        $data = $request->validated();
        $this->goodsService->deleteByIds($data);

        return $this->success([], "操作成功");
    }

    /**
     * 商品售罄
     *
     * @param SoldOutRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function soldOut(SoldOutRequest $request)
    {
        $data = $request->validated();
        $this->goodsService->soldOut($data);

        return $this->success([], "操作成功");
    }

    /**
     * 编辑商品
     *
     * @param EditRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function edit(EditRequest $request)
    {
        $data = $request->validated();

        $event = new GoodsEditEvent($data);
        event($event);

        $data['attr_color_list'] = $event->getAttrColorList();
        $data['cat'] = $event->getCategory();

        $this->goodsService->goodsEdit($data);

        return $this->success([], "操作成功");
    }

    /**
     * 编辑商品标签
     * @param EditTagRequest $request
     * @return array|mixed
     * @throws DbErrorException
     */
    public function editTag(EditTagRequest $request)
    {
        $data = $request->validated();

        $this->goodsService->editTag($data);

        return $this->success([], "操作成功");
    }
}
