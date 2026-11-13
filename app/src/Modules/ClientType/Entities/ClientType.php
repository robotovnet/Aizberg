<?php

declare(strict_types=1);

namespace Modules\ClientType\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\PriceType\Entities\PriceType;
use Eloquent;
use Modules\TransportCompany\Entities\PaymentMethod;

/**
 * Model ClientType.
 * @package Modules\ClientType\Entities
 * @mixin Eloquent
 */
class ClientType extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = ['name', 'price_type_id', 'is_default'];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = ['client_types.id', 'name', 'price_type_id', 'is_default'];

    /**
     * Relationship with the price types.
     */
    public function priceType(): BelongsTo
    {
        return $this->belongsTo(PriceType::class);
    }

    /**
     * Relationship: Payment Methods of the transport company.
     */
    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class);
    }
}
