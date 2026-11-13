<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Eloquent;

/**
 * Class Filter
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class Filter extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'catalog_filters';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'product_group_id',
        'feature_id',
        'order',
        'type',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'product_group_id',
        'feature_id',
        'order',
        'type',
    ];
}
