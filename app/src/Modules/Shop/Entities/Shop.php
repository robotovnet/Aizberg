<?php

declare(strict_types=1);

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\City\Entities\City;
use Eloquent;

/**
 * Class Shop
 * @package Modules\Shop\Entities
 * @mixin Eloquent
 */
class Shop extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = ['subdomain', 'city_id', 'city_name_dative_case', 'phone', 'is_active'];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = ['id', 'subdomain', 'city_id', 'city_name_dative_case', 'phone', 'is_active'];

    /**
     * Relationship with the cities.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
