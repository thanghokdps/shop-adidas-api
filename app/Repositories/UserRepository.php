<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserRepository.
 *
 * @package namespace App\Repositories;
 * @method whereIn(string $string, array $ids)
 * @method withTrashed()
 * @method onlyTrashed()
 * @method whereNotIn(string $string, int[] $array)
 */
interface UserRepository extends RepositoryInterface
{
    //
}
