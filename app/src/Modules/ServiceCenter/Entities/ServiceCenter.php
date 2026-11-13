<?php

namespace Modules\ServiceCenter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Modules\City\Entities\City;
use Modules\Country\Entities\Country;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class ServiceCenter
 * @package Modules\ServiceCenter\Entities
 * @mixin Builder
 */
class ServiceCenter extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'name',
        'address',
        'address_comment',
        'country_id',
        'city_id',
        'postal_code',
        'phone',
        'site',
        'lat',
        'lng',
        'metro_station',
        'work_time',
        'break_work_time',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'name',
        'id',
        'address',
        'address_comment',
        'country_id',
        'city_id',
        'postal_code',
        'phone',
        'site',
        'lat',
        'lng',
        'metro_station',
        'work_time',
        'break_work_time',
    ];

    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'id', 'city_id')
            ->withTrashed();
    }

    public function country(): HasOne
    {
        return $this->hasOne(Country::class, 'id', 'country_id')
            ->withTrashed();
        ;
    }
}
