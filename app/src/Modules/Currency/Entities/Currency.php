<?php

declare(strict_types=1);

namespace Modules\Currency\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Client\Entities\Client;

/**
 * Model Currency.
 * @package Modules\Currency\Entities
 * @mixin Eloquent
 */
class Currency extends Model
{
    use SoftDeletes;

    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = ['name', 'code', 'symbol', 'country_code', 'is_default', 'extra_charge'];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = ['id', 'name', 'code', 'symbol', 'country_code', 'is_default', 'extra_charge'];

    /**
     * Relationship 'Many To Many' with the client.
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }
}
