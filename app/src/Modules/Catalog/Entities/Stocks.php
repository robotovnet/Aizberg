<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class Stocks
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class Stocks extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'stocks';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'warehouse_id',
        'product_id',
        'qty',
        'updated_at',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'warehouse_id',
        'product_id',
        'qty',
    ];
}
