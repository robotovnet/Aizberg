<?php

declare(strict_types=1);

namespace Modules\Ad\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Nwidart\Modules\Facades\Module;

/**
 * Class YmlPolicy
 * @package Modules\Ad\Policies
 */
class YmlPolicy
{
    use HandlesAuthorization;

    /** @var string Stores the name of the entity. */
    private const ENTITY = 'yml';

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

        return $moduleName . '.' . self::ENTITY . '.' . $functionName;
    }
}
