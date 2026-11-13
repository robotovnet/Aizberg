<?php

declare(strict_types=1);

namespace Modules\PriceType\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model PriceType
 * @package Modules\PriceType\Entities
 * @mixin Eloquent
 */
class PriceType extends Model
{
    use SoftDeletes;

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = ['id', 'name', 'code'];

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = ['name', 'code'];
}
