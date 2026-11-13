<?php

declare(strict_types=1);

namespace Modules\Country\Entities;

use Illuminate\Database\Eloquent\Model;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Client\Entities\Client;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model Country.
 * @package Modules\Country\Entities
 * @mixin Eloquent
 */
class Country extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = ['name', 'code', 'language', 'is_default'];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = ['countries.id', 'countries.name', 'code', 'language', 'countries.is_default'];

    /**
     * Relationship: Client countries.
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }
}
