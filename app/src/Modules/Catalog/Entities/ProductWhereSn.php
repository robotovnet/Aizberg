<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class ProductWhereSn
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class ProductWhereSn extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'product_where_sn';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = false;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'product_id',
        'where_desc',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'product_id',
        'where_desc',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
