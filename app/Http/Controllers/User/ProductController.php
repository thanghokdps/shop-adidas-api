<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Services\DetailProductService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController
{
    protected $productService;
    protected $detailProductService;

    public function __construct(
        ProductService $productService,
        DetailProductService $detailProductService
    ) {
        $this->productService = $productService;
        $this->detailProductService = $detailProductService;
    }

    public function index():JsonResponse
    {
        return ResponseHelper::send($this->productService->all());
    }

    public function getDetailProduct($id): JsonResponse
    {
        return ResponseHelper::send($this->productService->findByField('id', $id));
    }

    public function updateView($id): JsonResponse
    {
        return ResponseHelper::send($this->productService->updateView($id));
    }

    public function search(Request $request): JsonResponse
    {
        return ResponseHelper::send($this->productService->search($request['name']));
    }
}
