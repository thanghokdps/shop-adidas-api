<?php

namespace App\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderRepository;
use App\Entities\Order;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class OrderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderRepositoryEloquent extends BaseRepository implements OrderRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Order::class;
    }


    /**
     * Boot up the repository, pushing criteria
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function insert(array $data)
    {
        return $this->model->insert($data);
    }

    public function budgetProduct()
    {
        return $this->model
            ->selectRaw('product_id, SUM(quantity) as quantity, SUM(products.price * quantity) as price, products.name')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->groupBy('product_id')
            ->orderBy('product_id')
            ->get();
    }

    public function budgetProductDetail($month, $year, $day, $group)
    {
        $sql = $this->model
            ->selectRaw('product_id, SUM(quantity) as quantity, SUM(products.price * quantity) as price, products.name')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->groupBy('product_id')
            ->orderBy('price','DESC');
        if($group == "day") {
            $sql = $sql->whereDate('orders.created_at', $day);
        } elseif($group == "month") {
            $sql = $sql->whereMonth('orders.created_at', $month)->whereYear('orders.created_at', $year);
        } elseif($group == "year") {
            $sql = $sql->whereYear('orders.created_at', $year);
        }
        return $sql->get();
    }

    public function budgetDate($month, $year, $day, $group)
    {
        $sql = "";
        if($group == "day") {
            $sql = $this->model->selectRaw('orders.created_at, SUM(products.price * quantity) as price')
                ->join('products', 'orders.product_id', '=', 'products.id')
                ->groupBy('created_at')
                ->orderBy('created_at','DESC')->whereMonth('orders.created_at', $month);
        } elseif($group == "month") {
            $sql = $this->model->selectRaw('MONTH(orders.created_at) as month, SUM(products.price * quantity) as price')
                ->join('products', 'orders.product_id', '=', 'products.id')
                ->groupBy('month')
                ->orderBy('month','DESC')->whereYear('orders.created_at', $year);
        } elseif($group == "year") {
            $sql = $this->model->selectRaw('YEAR(orders.created_at) as year, SUM(products.price * quantity) as price')
                ->join('products', 'orders.product_id', '=', 'products.id')
                ->groupBy('year')
                ->orderBy('year','DESC')->whereYear('orders.created_at', ">=", intval($year) - 4);
        }
        return $sql->get();
    }
}
