<?php

declare(strict_types=1);

namespace Modules\Staff\Services;

use Modules\Staff\Repositories\StaffPermissionRepository;

/**
 * Class PermissionsService
 * @package Modules\Staff\Services
 */
class PermissionsService
{
    /** @var StaffPermissionRepository An instance of the Staff Permission Repository */
    private StaffPermissionRepository $permissionRepo;

    public function __construct(StaffPermissionRepository $permissionRepo)
    {
        $this->permissionRepo = $permissionRepo;
    }

    public function getPermissionIds(array $permissions): array
    {
        $permissionsByPolicyNames = collect($permissions)->flatten()->toArray();

        return $this->permissionRepo->getIdsByPolicyNames($permissionsByPolicyNames);
    }
}
