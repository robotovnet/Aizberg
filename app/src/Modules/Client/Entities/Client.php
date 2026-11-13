<?php

declare(strict_types=1);

namespace Modules\Client\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Modules\ClientType\Entities\ClientType;
use Modules\Country\Entities\Country;
use Modules\Currency\Entities\Currency;
use Modules\Staff\Entities\Staff;
use Modules\Timezone\Entities\Timezone;
use Modules\Language\Entities\Language;
use Modules\City\Entities\City;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\BonusProgram\Entities\BonusLevel;
use Modules\Order\Entities\Order;

/**
 * Model Client.
 * @package Modules\Client\Entities
 * @mixin Eloquent
 */
class Client extends Authenticatable
{
    use SoftDeletes;

    /** @var string[] Massively assignable model attributes. */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'permanent_type',
    ];

    public const SELECT_ATTRIBUTES = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'deleted_at',
        'permanent_type',
    ];

    /** @var string[] List of attributes for request of select. */
    public const EXCEPT_ATTRIBUTES = [
        'password',
        'remember_token',
        'updated_at',
        //'deleted_at',
    ];

    /**
     * Relationship: User's country.
     */
    public function country(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    /**
     * Relationship: User's currency.
     */
    public function currency(): BelongsToMany
    {
        return $this->belongsToMany(Currency::class);
    }

    /**
     * Relationship: User's timezone.
     */
    public function timezone(): BelongsToMany
    {
        return $this->belongsToMany(Timezone::class);
    }

    /**
     * Relationship: User's language.
     */
    public function language(): BelongsToMany
    {
        return $this->belongsToMany(Language::class);
    }

    /**
     * Relationship: User's city.
     */
    public function city(): BelongsToMany
    {
        return $this->belongsToMany(City::class);
    }

    /**
     * Relationship: User's type.
     */
    public function clientType(): BelongsToMany
    {
        return $this->belongsToMany(ClientType::class);
    }

    /**
     * Relationship: User's bonus level.
     */
    public function bonusLevel(): BelongsToMany
    {
        return $this->belongsToMany(BonusLevel::class);
    }

    /**
     * Relationship: Client manager.
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class);
    }

    /**
     * Hashing the password.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id', 'id');
    }

    public function userpic(): HasOne
    {
        return $this->hasOne(ClientUserpic::class, 'client_id');
    }
}
