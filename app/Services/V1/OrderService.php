<?php

namespace App\Services\V1;

use App\Repositories\V1\OrderRepository;

class OrderService extends BaseService
{
    protected $orderRepo;
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepo = $orderRepository;
    }
}
