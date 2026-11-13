<?php

declare(strict_types=1);

namespace Modules\Staff\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class Staff
 * @package Modules\Staff\Entities
 * @mixin Eloquent
 */
class Staff extends Authenticatable
{
    use SoftDeletes;

    /** @var string[] List of attributes for request of select. */
    public const SELECT_ATTRIBUTES = ['id', 'first_name', 'last_name', 'phone', 'email', 'userpic_name', 'is_blocked', 'whatsapp', 'telegram', 'max'];

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = ['first_name', 'last_name', 'phone', 'email', 'password', 'userpic_name', 'whatsapp', 'telegram', 'max'];

    /**
     * Relationship: Returns the groups the staff belongs to.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(StaffGroup::class);
    }

    /**
     * Hashing the password.
     */
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Checks if the user has a specific permission.
     */
    public function hasPermission(string $policy): bool
    {
        $permissions = ($this->with('groups.permissions')->find(Auth::user()->id))
            ->groups->pluck('permissions')->flatten();

        $result = $permissions->first(function ($permission) use ($policy) {
            return $permission->policy == $policy;
        });

        return !is_null($result);
    }

    /*
     * Returns the all permissions of the user
     */
    public function getAllPermission(): array
    {
        $allPermissions = ($this->with(['groups.permissions' => function ($query) {
            $query->select(StaffPermission::SELECT_ATTRIBUTES);
        }])->find(Auth::user()->id))->groups->pluck('permissions')->flatten()->toArray();

        foreach ($allPermissions as &$permission) {
            Arr::forget($permission, 'pivot');
        }

        return Arr::onlyActiveLang(
            StaffPermission::LOCALIZATION_ATTRIBUTES,
            App::getLocale(),
            $allPermissions
        );
    }

    /*
     * Returns the groups in which the staff is a member
     */
    public function getGroups(): array
    {
        $userId = (isset(Auth::user()->id)) ? Auth::user()->id : Auth::guard('staff')->user()->id;

        $groups = ($this->with(['groups' => function ($query) {
            $query->select(StaffGroup::SELECT_ATTRIBUTES);
        }])->find($userId))->groups;

        foreach ($groups as &$group) {
            Arr::forget($group, 'pivot');
        }

        return $groups->toArray();
    }

    /*
     * Returns the all policies of the user
     */
    public function getAllPolicy(): array
    {
        $policies = [];
        $permissions = $this->getAllPermission();

        foreach ($permissions as $permission) {
            $policies[] = $permission['policy'];
        }

        return $policies;
    }

    /*
     * Returns the full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
