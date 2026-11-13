<?php

declare(strict_types=1);

namespace Modules\Timezone\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Client\Entities\Client;

/**
 * Model Timezone.
 * @package Modules\Timezone\Entities
 * @mixin Eloquent
 */
class Timezone extends Model
{
    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = false;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [];

    /**
     * Relationship 'Many To Many' with the client.
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }
}
