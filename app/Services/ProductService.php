<?php

namespace App\Services;

use App\Repositories\DetailProductRepository;
use App\Repositories\ProductRepository;

class ProductService
{
    protected $productRepository;
    protected $detailProductRepository;

    public function __construct(
        ProductRepository $productRepository,
        DetailProductRepository $detailProductRepository
    ){
        $this->productRepository = $productRepository;
        $this->detailProductRepository = $detailProductRepository;
    }

    public function all()
    {
        return $this->productRepository->with(['detailProducts'])->allByCreated();
    }

    public function findByField($field, $value, $columns = ['*'])
    {
        return $this->productRepository->with(['detailProducts'])->findByField($field, $value, $columns);
    }

    public function create(array $attributes)
    {
        return $this->productRepository->create($attributes);
    }

    public function update(array $attributes, $id)
    {
        return $this->productRepository->update($attributes, $id);
    }

    public function deleteProducts(array $ids)
    {
        $this->detailProductRepository->whereIn('product_id', $ids)->delete();
        return $this->productRepository->whereIn('id', $ids)->delete();
    }

    public function getDeletedProducts()
    {
        return $this->productRepository->onlyTrashed()->get();
    }

    public function updateDeletedProducts(array $ids)
    {
        return $this->productRepository->withTrashed()->whereIn('id', $ids)->restore();
    }

    public function updateView($id)
    {
        return $this->productRepository->updateView($id);
    }

    public function search($string)
    {
        return $this->productRepository->search($string);
    }
}
