<?php

declare(strict_types=1);

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class Group
 * @package Modules\Catalog\Entities
 * @mixin Eloquent
 */
class Group extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'product_groups';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'plural_name',
        'singular_name',
        'overview_text',
        'replacement_only',
        'hidden_for_service',
        'where_sn_desc',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'plural_name',
        'singular_name',
        'overview_text',
        'replacement_only',
        'hidden_for_service',
        'where_sn_desc',
    ];

    public function features(): HasMany
    {
        return $this->hasMany(GroupFeature::class);
    }
}
