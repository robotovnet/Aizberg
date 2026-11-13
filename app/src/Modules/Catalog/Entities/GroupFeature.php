<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class Feature
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class GroupFeature extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'product_groups_features';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'group_id',
        'feature_id',
        'default_value',
        'overview_text',
        'is_required',
        'is_main',
        'order',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'group_id',
        'feature_id',
        'default_value',
        'overview_text',
        'is_required',
        'is_main',
        'order',
    ];

    public function featureData(): HasMany
    {
        return $this->hasMany(Feature::class, 'id', 'feature_id');
    }
}
