<?php

namespace Modules\TransportCompany\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class Postamat
 * @package Modules\TransportCompany\Entities
 * @mixin Eloquent
 */
class Postamat extends Model
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
        'payment_by_card',
        'payment_by_cash',
        'postal_code',
        'phone',
        'lat',
        'lng',
        'metro_station',
        'work_time',
        'break_work_time',
        'adding_method',
        'max_height',
        'max_width',
        'max_depth',
        'tc_id',
        'rates',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'name',
        'id',
        'address',
        'address_comment',
        'country_id',
        'city_id',
        'payment_by_card',
        'payment_by_cash',
        'postal_code',
        'phone',
        'lat',
        'lng',
        'metro_station',
        'work_time',
        'break_work_time',
        'adding_method',
        'max_height',
        'max_width',
        'max_depth',
        'tc_id',
        'rates',
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
}
