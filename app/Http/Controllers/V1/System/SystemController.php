<?php

namespace App\Http\Controllers\V1\System;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Store\HqResource;
use App\Services\V1\SystemService;
use Exception;

class SystemController extends Controller
{
    protected $systemService;

    public function __construct(SystemService $systemService)
    {
        $this->systemService = $systemService;
    }

    /**
     * 获取用户管理的HQ列表
     *
     * @return array|mixed
     * @throws Exception
     */
    public function systemSet()
    {
        $data = $this->systemService->systemSet();
        return $this->success($data);
    }
}
