<?php

namespace App\Http\Controllers\V1\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Store\Index\CreateRequest;
use App\Http\Requests\V1\Store\Index\UpdateRequest;
use App\Http\Resources\V1\Store\HqResource;
use App\Http\Resources\V1\Store\StoreResource;
use App\Services\V1\HqService;
use App\Services\V1\StoreService;
use Exception;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    /**
     * 获取店铺列表
     * @param HqService $hqService
     * @return mixed
     * @throws Exception
     */
    public function storeList(HqService $hqService)
    {
        $hqList = $hqService->getHqListByAdmin();
        $storeList = $this->storeService->getStoreListByAdmin();
        return $this->success(
            [
                "hq_list" => HqResource::collection($hqList),
                "store_list" => StoreResource::collection($storeList)
            ]
        );
    }

    /**
     * 获取店铺详情
     * @param Request $request
     * @param int $storeId 店铺ID
     * @return mixed|void
     * @throws Exception
     */
    public function storeInfo(Request $request, int $storeId)
    {
        $storeInfo = $this->storeService->getStoreInfo($storeId);

        return $this->success(new StoreResource($storeInfo));
    }

    /**
     *  创建店铺
     * @param CreateRequest $request
     * @return mixed
     * @throws Exception
     */
    public function create(CreateRequest $request)
    {
        $data = $request->validated();
        $store = $this->storeService->createStore($data);
        return $this->success(new StoreResource($store));
    }

    /**
     * 修改店铺信息
     * @param UpdateRequest $request
     * @return mixed|void
     * @throws Exception
     */
    public function update(UpdateRequest $request)
    {
        $data = $request->validated();
        return $this->storeService->updateStore($data);
    }

    /**
     * 记录Admin进入HQ
     *
     * @param HqService $hqService
     * @param int $hqId HQ ID
     * @return mixed
     * @throws Exception
     */
    public function intoHq(HqService $hqService, int $hqId)
    {
        $hqService->intoHq($hqId);

        return $this->success();
    }

    /**
     * 记录Admin进入店铺
     *
     * @param int $storeId 店铺ID
     * @return bool
     * @throws Exception
     */
    public function intoStore(int $storeId)
    {
        $this->storeService->intoStore($storeId);

        return $this->success();
    }
}
