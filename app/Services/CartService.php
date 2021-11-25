<?php

namespace App\Services;

use App\Repositories\CartRepository;

class CartService
{
    protected $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function all()
    {
        return $this->cartRepository->all();
    }

    public function findByField($field, $value, $columns = ['*'])
    {
        return $this->cartRepository->findByField($field, $value, $columns);
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->cartRepository->updateOrCreate($attributes, $values);
    }

    public function delete($id): int
    {
        return $this->cartRepository->delete($id);
    }
}
