<?php

declare(strict_types=1);

namespace Modules\OrderOneClick\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;
use Modules\Catalog\Entities\Product;

/**
 * Class OneClickOrder
 * @package Modules\OrderOneClick\Entities
 * @mixin Eloquent
 */
class OneClickOrder extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'one_click_orders';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'is_completed',
        'product_id',
        'client_phone',
        'client_email',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'one_click_orders.id',
        'is_completed',
        'product_id',
        'client_phone',
        'client_email',
        'created_at',
    ];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
