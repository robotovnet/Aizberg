<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class AdGenerator
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class AdGenerator extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'product_ad_generator';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'product_id',
        'alt_names',
        'pn'
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'product_id',
        'alt_names',
        'pn',
    ];

    public function adGenerator(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
