<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

/**
 * Class PaymentMethodPolicy
 * @package Modules\TransportCompany\Policies
 */
class PaymentMethodPolicy
{
    use HandlesAuthorization;

    public const MODULE_NAME = 'paymentmethods';

    public function management(): bool
    {
        return Auth::user()->hasPermission($this->getPermissionName(__FUNCTION__));
    }

    public function view(): bool
    {
        return Auth::user()->hasPermission($this->getPermissionName(__FUNCTION__));
    }

    /**
     * Forms the name of the permission.
     */
    private function getPermissionName(string $functionName): string
    {
        return self::MODULE_NAME . '.' . $functionName;
    }
}
// dsf fsdfds
