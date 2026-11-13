<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Eloquent;

/**
 * Class ProductAdsRotator
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class ProductAdsRotator extends Model
{
    /** @var string Table name. */
    protected $table = 'product_ads_rotator';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = false;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'product_id',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'product_id',
    ];
}
