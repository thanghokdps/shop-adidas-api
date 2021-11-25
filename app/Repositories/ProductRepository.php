<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ProductRepository.
 *
 * @package namespace App\Repositories;
 * @method whereIn(string $string, array $ids)
 * @method onlyTrashed()
 * @method withTrashed()
 */
interface ProductRepository extends RepositoryInterface
{
    public function updateView($id);
    public function search($string);
    public function allByCreated();
}
