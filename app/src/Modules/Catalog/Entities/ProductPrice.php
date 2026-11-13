<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class ProductPrice
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class ProductPrice extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'product_prices';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'price_type_id',
        'product_id',
        'value',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'product_id',
        'price_type_id',
        'value',
    ];

    public function prices(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
