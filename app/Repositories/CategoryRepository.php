<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CategoryRepository.
 *
 * @package namespace App\Repositories;
 * @method whereIn(string $string, array $ids)
 * @method withTrashed()
 * @method onlyTrashed()
 */
interface CategoryRepository extends RepositoryInterface
{
    //
}
