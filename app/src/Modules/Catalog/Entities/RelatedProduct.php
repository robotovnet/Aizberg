<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class RelatedProduct
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class RelatedProduct extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'related_products';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'product_id',
        'related_product_id',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'product_id',
        'related_product_id',
    ];

    public function relatedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
