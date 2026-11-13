<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class SimilarProduct
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class SimilarProduct extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'similar_products';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'product_id',
        'similar_product_id',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'product_id',
        'similar_product_id',
    ];

    public function similarProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
