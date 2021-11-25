<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class CategoryController
{
    protected $categoryService;
    protected $productService;

    public function __construct(
        CategoryService $categoryService,
        ProductService $productService
    ) {
        $this->categoryService = $categoryService;
        $this->productService = $productService;
    }

    public function index(): JsonResponse
    {
        $data = $this->categoryService->all()->toArray();
        $categories = array_filter(
            $data,
            function ($var) {
                return is_null($var['type']);
            }
        );
        foreach($categories as $key => $category) {
            $subs = array_filter(
                $data,
                function ($var) use ($category) {
                    return $var['type'] == $category['id'];
                }
            );
            $categories[$key]['subs'] = array_values($subs);
        }
        return ResponseHelper::send(array_values($categories));
    }

    public function getProductsForCategory($id): JsonResponse
    {
        return ResponseHelper::send($this->productService->findByField('category_id', $id));
    }
}
