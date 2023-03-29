<?php

namespace App\Http\Controllers\V1\Common;

use App\Http\Controllers\Controller;
use App\Services\V1\WeChat\WeChatService;
use Exception;

class WeChatController extends Controller
{
    protected $weChatService;

    public function __construct(WeChatService $weChatService)
    {
        $this->weChatService = $weChatService;
    }

    /**
     * 回应微信服务器验证
     * @return mixed
     * @throws Exception
     */
    public function serve()
    {
        return $this->weChatService->serve();
    }
}
