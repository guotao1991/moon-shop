<?php

namespace App\Http\Controllers\V1\Hq;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Store\HqResource;
use App\Services\V1\HqService;
use Exception;

class HqController extends Controller
{
    protected $hqService;

    public function __construct(HqService $hqService)
    {
        $this->hqService = $hqService;
    }

    /**
     * 获取用户管理的HQ列表
     *
     * @return array|mixed
     * @throws Exception
     */
    public function hqList()
    {
        $list = $this->hqService->hqList();
        return $this->success(['list' => HqResource::collection($list)]);
    }
}
