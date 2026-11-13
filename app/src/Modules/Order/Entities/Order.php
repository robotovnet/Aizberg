<?php

declare(strict_types=1);

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\City\Entities\City;
use Modules\Client\Entities\Client;
use Modules\Shop\Entities\Shop;
use Modules\Staff\Entities\Staff;
use Modules\TransportCompany\Entities\PaymentMethod;
use Modules\TransportCompany\Entities\TransportCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Order
 * @package Modules\Order\Entities
 * @mixin Eloquent
 */
class Order extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'client_id',
        'staff_id',
        'order_status',
        'shop_id',
        'city_id',
        'payment_method_id',
        'payment_status',
        'transport_company_id',
        'delivery_point_type',
        'delivery_point_id',
        'track_number',
        'delivery_cost',
        'total_products_cost',
        'total_order_cost',
        'address_comment',
        'order_comment',
        'delivery_address',
        'is_mobile_order',
        'completed_at',
        'viewed',
        'client_email',
        'client_phone',
        'client_phone_add',
        'client_name',
        'client_surname',
        'referrer',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'orders.id',
        'client_id',
        'staff_id',
        'order_status',
        'shop_id',
        'city_id',
        'payment_method_id',
        'payment_status',
        'transport_company_id',
        'delivery_point_type',
        'delivery_point_id',
        'track_number',
        'delivery_cost',
        'total_products_cost',
        'total_order_cost',
        'address_comment',
        'order_comment',
        'delivery_address',
        'is_mobile_order',
        'completed_at',
        'viewed',
        'client_email',
        'client_phone',
        'client_phone_add',
        'client_name',
        'client_surname',
        'referrer',
        'orders.created_at'
    ];

    /**
     * Relationship: Client who placed the order.
     */
    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id')
            ->withTrashed();
    }

    /**
     * Relationship: Order processing manager.
     */
    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class, 'id', 'staff_id')
            ->withTrashed();
    }

    /**
     * Relationship: The shop where the order was placed.
     */
    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class, 'id', 'shop_id')
            ->withTrashed();
    }

    /**
     * Relationship: The city in which the order was placed.
     */
    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'id', 'city_id')
            ->withTrashed();
    }

    /**
     * Relationship: Payment method by which the order is paid.
     */
    public function paymentMethod(): HasOne
    {
        return $this->hasOne(PaymentMethod::class, 'id', 'payment_method_id')
            ->withTrashed();
    }

    /**
     * Relationship: Transport company that will deliver the order.
     */
    public function transportCompany(): HasOne
    {
        return $this->hasOne(TransportCompany::class, 'id', 'transport_company_id')
            ->withTrashed();
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)
            ->withTrashed();
    }
}
