<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Order\Index\CreateRequest;
use App\Services\V1\OrderService;

class IndexController extends Controller
{

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @param CreateRequest $request
     */
    public function create(CreateRequest $request)
    {
    }
}
