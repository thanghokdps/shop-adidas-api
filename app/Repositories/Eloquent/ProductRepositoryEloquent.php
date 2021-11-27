<?php

namespace App\Repositories\Eloquent;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\Product;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class ProductRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductRepositoryEloquent extends BaseRepository implements ProductRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Product::class;
    }


    /**
     * Boot up the repository, pushing criteria
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function updateView($id)
    {
        return DB::statement("UPDATE products SET view = view + 1 WHERE id = $id");
    }

    public function search($string)
    {
        return $this->model->with(['detailProducts'])->where('name', 'like', '%' . $string . '%')->get();
    }

    public function allByCreated()
    {
        return $this->model->orderByDesc('created_at')->get();
    }
}
