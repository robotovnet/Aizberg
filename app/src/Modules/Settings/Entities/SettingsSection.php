<?php

declare(strict_types=1);

namespace Modules\Settings\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model SettingsSection.
 * @package Modules\Settings\Entities
 * @mixin Eloquent
 */
class SettingsSection extends Model
{
    /** @var bool Support for "created_at" and "updated_at" columns. */
    public $timestamps = true;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = ['name', 'code', 'order'];

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = ['id', 'name', 'code', 'order'];

    /**
     * Relationship 'One To Many' with the section items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Settings::class, 'section_code', 'code');
    }
}
