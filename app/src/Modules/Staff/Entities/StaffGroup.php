<?php

declare(strict_types=1);

namespace Modules\Staff\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class StaffGroup
 * @package Modules\Staff\Entities
 * @mixin Eloquent
 */
class StaffGroup extends Model
{
    use SoftDeletes;

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = ['name', 'staff_groups.id', 'start_policy'];

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = ['name', 'start_policy'];

    /**
     * Relationship: The permissions of staff included in a specific group.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(StaffPermission::class);
    }

    /**
     * Relationship: List of group members.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class);
    }
}
