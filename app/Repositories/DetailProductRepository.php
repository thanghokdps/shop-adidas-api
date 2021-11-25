<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface DetailProductRepository.
 *
 * @package namespace App\Repositories;
 * @method whereIn(string $string, array $ids)
 */
interface DetailProductRepository extends RepositoryInterface
{
    public function updateQuantity($quantity, $productId, $size);
}
