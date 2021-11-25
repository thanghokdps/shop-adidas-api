<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Request\CreateOrUpdateDetailProductRequest;
use App\Http\Request\DeleteOrUpdateDeletedRequest;
use App\Services\DetailProductService;
use Illuminate\Http\JsonResponse;

class DetailProductController
{
    protected $detailProductService;

    public function __construct(DetailProductService $detailProductService)
    {
        $this->detailProductService = $detailProductService;
    }

    public function index(): JsonResponse
    {
        return ResponseHelper::send($this->detailProductService->all());
    }

    public function getDetail($id): JsonResponse
    {
        return ResponseHelper::send($this->detailProductService->findByField('product_id', $id));
    }

    public function createOrUpdate(CreateOrUpdateDetailProductRequest $request): JsonResponse
    {
        return ResponseHelper::send($this->detailProductService->updateOrCreate(
            [
                'product_id' => $request['product_id'],
                'size' => $request['size']
            ],
            $request->all()));
    }

    public function deleteDetailProducts(DeleteOrUpdateDeletedRequest $request): JsonResponse
    {
        return ResponseHelper::send($this->detailProductService->deleteDetailProducts($request['ids']));
    }
}
