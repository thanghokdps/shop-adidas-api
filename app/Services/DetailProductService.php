<?php

namespace App\Services;

use App\Repositories\DetailProductRepository;

class DetailProductService
{
    protected $detailProductRepository;

    public function __construct(DetailProductRepository $detailProductRepository)
    {
        $this->detailProductRepository = $detailProductRepository;
    }

    public function all() {
        return $this->detailProductRepository->all();
    }

    public function findByField($field, $value, $columns = ['*'])
    {
        return $this->detailProductRepository->findByField($field, $value, $columns);
    }

    public function update(array $attributes)
    {
        foreach ($attributes as $detailProduct)
        {
            $this->detailProductRepository->updateQuantity($detailProduct['quantity'],$detailProduct['product_id'],$detailProduct['size']);
        }
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->detailProductRepository->updateOrCreate($attributes, $values);
    }

    public function delete($id): int
    {
        return $this->detailProductRepository->delete($id);
    }

    public function deleteDetailProducts(array $ids)
    {
        return $this->detailProductRepository->whereIn('id', $ids)->delete();
    }
}
