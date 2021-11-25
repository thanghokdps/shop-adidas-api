<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @method static where(string $string, $userId)
 */
class Transaction extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'user_id',
        'user_name',
        'user_email',
        'user_phone',
        'user_address',
        'amount',
        'payment',
        'shipping',
        'message'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'transaction_id', 'id');
    }
}
