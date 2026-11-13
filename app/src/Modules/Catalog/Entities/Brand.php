<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class Brand
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class Brand extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'brands';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'name',
        'country_code',
        'official_website',
        'desc',
        'is_hidden',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'name',
        'country_code',
        'official_website',
        'desc',
        'is_hidden',
    ];
}
