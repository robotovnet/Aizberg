<?php

declare(strict_types=1);

namespace Modules\City\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Client\Entities\Client;

/**
 * Model City.
 * @package Modules\City\Entities
 * @mixin Eloquent
 */
class City extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = ['name', 'country_code', 'timezone', 'is_default'];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = ['cities.id', 'name', 'country_code', 'timezone', 'is_default'];

    /** @var string[] List of attributes to be localized. */
    public const LOCALIZATION_ATTRIBUTES = ['name'];

    /**
     * Relationship 'Many To Many' with the client.
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }
}
