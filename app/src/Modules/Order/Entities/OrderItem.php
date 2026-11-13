<?php

declare(strict_types=1);

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Catalog\Entities\Product;

/**
 * Class OrderItem
 * @package Modules\Order\Entities
 * @mixin Eloquent
 */
class OrderItem extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total_cost',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'order_items.id',
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total_cost',
    ];

    /**
     * Relationship: Products.
     */
    public function product(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items', 'id', 'product_id');
    }
}
