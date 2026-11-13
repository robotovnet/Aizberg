<?php

namespace Modules\TransportCompany\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;
use Modules\ClientType\Entities\ClientType;

/**
 * Class PaymentMethod
 * @package Modules\TransportCompany\Entities
 * @mixin Eloquent
 */
class PaymentMethod extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'name',
        'additional_comment',
        'code',
        'minimum_order_value',
        'maximum_order_value',
        'order',
        'is_active',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'name',
        'additional_comment',
        'code',
        'minimum_order_value',
        'maximum_order_value',
        'order',
        'is_active',
    ];

    /**
     * Relationship: Client types of the payment method.
     */
    public function clientTypes(): BelongsToMany
    {
        return $this->belongsToMany(ClientType::class);
    }

    /**
     * Relationship: Transport companies of the payment method.
     */
    public function transportCompanies(): BelongsToMany
    {
        return $this->belongsToMany(TransportCompany::class)
            ->withPivot('is_support_courier_delivery', 'is_support_postamats', 'is_support_pickup_points');
    }
}
