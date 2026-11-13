<?php

declare(strict_types=1);

namespace Modules\ImageUploader\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Nwidart\Modules\Facades\Module;

class ImageUploadPolicy
{
    use HandlesAuthorization;

    public function management(): bool
    {
        return Auth::user()?->hasPermission($this->getPermissionName(__FUNCTION__)) ?? false;
    }

    public function view(): bool
    {
        return Auth::user()?->hasPermission($this->getPermissionName(__FUNCTION__)) ?? false;
    }

    private function getPermissionName(string $functionName): string
    {
        $moduleName = Module::getModuleName(__NAMESPACE__);

        return $moduleName . '.' . $functionName;
    }
}

