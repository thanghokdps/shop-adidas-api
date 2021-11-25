<?php

namespace App\Repositories\Eloquent;

use App\Repositories\DetailProductRepository;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\DetailProduct;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class DetailProductRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DetailProductRepositoryEloquent extends BaseRepository implements DetailProductRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return DetailProduct::class;
    }


    /**
     * Boot up the repository, pushing criteria
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function updateQuantity($quantity, $productId, $size)
    {
        return DB::statement("UPDATE detail_products SET quantity = quantity - ($quantity) WHERE product_id = $productId AND size =  $size");
    }
}
