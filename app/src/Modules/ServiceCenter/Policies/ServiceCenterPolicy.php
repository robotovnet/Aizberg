<?php

declare(strict_types=1);

namespace Modules\ServiceCenter\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Nwidart\Modules\Facades\Module;

/**
 * Class ServiceCenterPolicy
 * @package Modules\ServiceCenter\Policies
 */
class ServiceCenterPolicy
{
    use HandlesAuthorization;

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
        $moduleName = Module::getModuleName(__NAMESPACE__);

        return $moduleName . '.' . $functionName;
    }
}
