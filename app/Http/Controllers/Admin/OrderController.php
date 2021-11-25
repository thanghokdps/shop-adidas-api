<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): JsonResponse
    {
        return ResponseHelper::send($this->orderService->all());
    }

    public function getDetail($id): JsonResponse
    {
        return ResponseHelper::send($this->orderService->findByField('transaction_id', $id));
    }

    public function budgetProduct(): JsonResponse
    {
        return ResponseHelper::send($this->orderService->budgetProduct());
    }

    public function budgetProductDetail(Request $request): JsonResponse
    {
        $group = $request['group'];
        $day = Carbon::parse($request['date'])->format('Y-m-d');
        $month = Carbon::parse($request['date'])->format('m');
        $year = Carbon::parse($request['date'])->format('Y');
        return ResponseHelper::send($this->orderService->budgetProductDetail($month, $year, $day, $group));
    }

    public function budgetDate(Request $request): JsonResponse
    {
        $group = $request['group'];
        $day = Carbon::parse($request['date'])->format('Y-m-d');
        $month = Carbon::parse($request['date'])->format('m');
        $year = Carbon::parse($request['date'])->format('Y');
        return ResponseHelper::send($this->orderService->budgetDate($month, $year, $day, $group));
    }
}
