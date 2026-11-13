<?php

declare(strict_types=1);

namespace Modules\BonusProgram\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Nwidart\Modules\Facades\Module;

/**
 * Class BonusLevelPolicy
 * @package Modules\BonusProgram\Policies
 */
class BonusLevelPolicy
{
    use HandlesAuthorization;

    /** @var string Stores the name of the entity. */
    private const ENTITY = 'level';

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
