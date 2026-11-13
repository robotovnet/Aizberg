<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Model TransportCompany
 * @package Modules\TransportCompany\Entities
 * @mixin Eloquent
 */
class TransportCompany extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'name',
        'additional_comment',
        'code',
        'accept_orders_start_time',
        'accept_orders_completion_time',
        'minimum_order_value_courier',
        'minimum_order_value_pickup',
        'icon_path',
        'is_support_courier_delivery',
        'is_support_postamats',
        'is_support_pickup_points',
        'is_active',
        'order'
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'transport_companies.id',
        'name',
        'additional_comment',
        'code',
        'accept_orders_start_time',
        'accept_orders_completion_time',
        'minimum_order_value_courier',
        'minimum_order_value_pickup',
        'icon_path',
        'transport_companies.is_support_courier_delivery',
        'transport_companies.is_support_postamats',
        'transport_companies.is_support_pickup_points',
        'is_active',
        'order'
    ];

    /**
     * Relationship: Delivery settings of the transport company.
     */
    public function deliverySettings(): HasMany
    {
        return $this->hasMany(DeliverySettings::class, 'transport_company_id');
    }

    /**
     * Relationship: Warehouses of the transport company.
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class);
    }

    /**
     * Relationship: Courier cities of the transport company.
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

    /**
     * Relationship: Payment Methods of the transport company.
     */
    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class);
    }

    /**
     * Relationship: Shipping costs (тарифы доставки) of the transport company.
     */
    public function shippingCosts(): HasMany
    {
        return $this->hasMany(ShippingCost::class, 'transport_company_id');
    }
}
