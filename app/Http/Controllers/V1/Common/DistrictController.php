<?php

namespace App\Http\Controllers\V1\Common;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Common\DistrictResource;
use App\Services\V1\DistrictService;
use Exception;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public $districtService;

    /**
     * DistrictController constructor.
     * @param DistrictService $districtService
     */
    public function __construct(DistrictService $districtService)
    {
        $this->districtService = $districtService;
    }

    /**
     * 获取省市区列表
     * @return array|mixed
     * @throws Exception
     */
    public function getDistrictList()
    {
        $list = $this->districtService->getDistrictList();

        return $this->success(['list' => DistrictResource::collection($list)]);
    }
}
