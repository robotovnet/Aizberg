<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Entities;

use Illuminate\Database\Eloquent\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\City\Entities\City;
use Modules\Country\Entities\Country;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class CourierCity
 * @package Modules\TransportCompany\Entities
 * @mixin Eloquent
 */
class CourierCity extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'country_id',
        'city_id',
        'adding_method',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'courier_cities.id',
        'country_id',
        'city_id',
        'adding_method',
    ];

    /**
     * Relationship: Transport companies of the courier city.
     */
    public function transportCompanies(): BelongsToMany
    {
        return $this->belongsToMany(TransportCompany::class);
    }

    /**
     * Relationship: Warehouses of the courier city.
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class);
    }

    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'id', 'city_id')
            ->withTrashed();
    }

    public function country(): HasOne
    {
        return $this->hasOne(Country::class, 'id', 'country_id')
            ->withTrashed();
    }
}
