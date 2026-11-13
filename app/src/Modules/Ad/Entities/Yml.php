<?php

declare(strict_types=1);

namespace Modules\Ad\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;
use Modules\TransportCompany\Entities\Warehouse;

/**
 * Class Yml
 * @package Modules\Ad\Entities
 * @mixin Eloquent
 */
class Yml extends Model
{
    use SoftDeletes;

    /** @var string Table name. */
    protected $table = 'ymls';

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'shop_name',
        'company_name',
        'shop_domain',
        'shop_id',
    ];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = [
        'id',
        'shop_name',
        'company_name',
        'shop_domain',
        'shop_id',
    ];

    /**
     * Remains of which warehouses to display?
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class);
    }
}
