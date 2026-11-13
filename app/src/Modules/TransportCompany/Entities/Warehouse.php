<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class Warehouse
 * @package Modules\TransportCompany\Entities
 * @mixin Eloquent
 */
class Warehouse extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'name',
        'label_1c',
        'code',
        'priority',
        'is_active',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'warehouses.id',
        'name',
        'label_1c',
        'code',
        'priority',
        'is_active',
    ];

    /**
     * Relationship: Transport companies of the warehouse.
     */
    public function transportCompanies(): BelongsToMany
    {
        return $this->belongsToMany(TransportCompany::class)
            ->withPivot('for_all_courier_cities', 'for_all_postamats', 'for_all_pickup_points');
    }

    /**
     * Relationship: Courier cities of the warehouse.
     */
    public function courierCities(): BelongsToMany
    {
        return $this->belongsToMany(CourierCity::class);
    }

    /**
     * Relationship: Pickup points of the transport company.
     */
    public function pickupPoints(): BelongsToMany
    {
        return $this->belongsToMany(PickupPoint::class);
    }

    /**
     * Relationship: Postamats of the transport company.
     */
    public function postamats(): BelongsToMany
    {
        return $this->belongsToMany(Postamat::class);
    }
}
