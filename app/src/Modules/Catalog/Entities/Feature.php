<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class Feature
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class Feature extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'name',
        'unit',
        'sort_type',
        'is_for_all_groups',
        'type',
        'value_separator',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'features.id',
        'name',
        'unit',
        'sort_type',
        'is_for_all_groups',
        'type',
        'value_separator',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
