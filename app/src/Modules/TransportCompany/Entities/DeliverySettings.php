<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class DeliverySettings
 * @package Modules\TransportCompany\Entities
 * @mixin Eloquent
 */
class DeliverySettings extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'days_transfer',
        'transfer_time_tomorrow',
        'transfer_time_after_tomorrow',
        'transport_company_working_days',
        'transport_company_weekends',
        'delivery_type',
        'delivery_point_id',
        'transport_company_id',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'days_transfer',
        'transfer_time_tomorrow',
        'transfer_time_after_tomorrow',
        'transport_company_working_days',
        'transport_company_weekends',
        'delivery_type',
        'delivery_point_id',
        'transport_company_id',
    ];

    protected $casts = [
        'transport_company_working_days' => 'array',
        'transport_company_weekends'     => 'array',
    ];

}
