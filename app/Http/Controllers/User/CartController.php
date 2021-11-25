<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Request\CreateOrUpdateCartRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;

class CartController
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(): JsonResponse
    {
        return ResponseHelper::send($this->cartService->findByField('user_id', auth('api')->user()['id']));
    }

    public function createOrUpdate(CreateOrUpdateCartRequest $request): JsonResponse
    {
        return ResponseHelper::send($this->cartService->updateOrCreate(
            [
                'user_id' => $request['user_id'],
                'product_id' => $request['product_id'],
                'size' => $request['size']
            ],
            $request->all()));
    }

    public function delete($id): JsonResponse
    {
        return ResponseHelper::send($this->cartService->delete($id));
    }
}
