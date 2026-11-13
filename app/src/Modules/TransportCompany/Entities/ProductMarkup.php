<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class ProductMarkup
 * @package Modules\TransportCompany\Entities
 * @mixin Eloquent
 */
class ProductMarkup extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'payment_method_id',
        'transport_company_id',
        'is_support_courier_delivery',
        'is_support_postamats',
        'is_support_pickup_points',
        'markup',
        'markup_type',
        'is_active',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'payment_method_id',
        'transport_company_id',
        'is_support_courier_delivery',
        'is_support_postamats',
        'is_support_pickup_points',
        'markup',
        'markup_type',
        'is_active',
    ];
}
