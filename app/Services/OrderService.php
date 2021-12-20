<?php

namespace App\Services;

use App\Repositories\OrderRepository;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function all()
    {
        return $this->orderRepository->with(['product'])->all();
    }

    public function findByField($field, $value, $columns = ['*'])
    {
        return $this->orderRepository->with(['product'])->findByField($field, $value, $columns);
    }

    public function insert(array $attributes)
    {
        return $this->orderRepository->insert($attributes);
    }

    public function delete($id)
    {
        return $this->orderRepository->where('transaction_id', $id)->delete();
    }

    public function deleteHard($id)
    {
        return $this->orderRepository->where('transaction_id', $id)->forceDelete();
    }

    public function budgetProduct()
    {
        return $this->orderRepository->budgetProduct();
    }

    public function budgetProductDetail($month, $year, $day, $group)
    {
        return $this->orderRepository->budgetProductDetail($month, $year, $day, $group);
    }

    public function budgetDate($month, $year, $day, $group)
    {
        return $this->orderRepository->budgetDate($month, $year, $day, $group);
    }

    public function updateIsComment($id)
    {
        return $this->orderRepository->update(['is_comment'=>true], $id);
    }
}
