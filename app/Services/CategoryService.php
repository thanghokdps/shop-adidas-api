<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\DetailProductRepository;
use App\Repositories\ProductRepository;

class CategoryService
{
    protected $categoryRepository;
    protected $productRepository;
    protected $detailProductRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        DetailProductRepository $detailProductRepository
    ){
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->detailProductRepository = $detailProductRepository;
    }

    public function all()
    {
        return $this->categoryRepository->all();
    }

    public function create(array $attributes)
    {
        return $this->categoryRepository->create($attributes);
    }

    public function update(array $attributes, $id)
    {
        return $this->categoryRepository->update($attributes, $id);
    }

    public function findByField($field, $value, $columns = ['*'])
    {
        return $this->categoryRepository->findByField($field, $value, $columns);
    }

    public function deleteCategories(array $ids)
    {
        $this->detailProductRepository->whereIn('product_id', $ids)->delete();
        $this->productRepository->whereIn('category_id', $ids)->delete();
        return $this->categoryRepository->whereIn('id', $ids)->delete();
    }

    public function getDeletedCategories()
    {
        return $this->categoryRepository->onlyTrashed()->get();
    }

    public function updateDeletedCategories(array $ids)
    {
        return $this->categoryRepository->withTrashed()->whereIn('id', $ids)->restore();
    }
}
