<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index($id): JsonResponse
    {
        return ResponseHelper::send($this->orderService->findByField('transaction_id', $id));
    }
}
