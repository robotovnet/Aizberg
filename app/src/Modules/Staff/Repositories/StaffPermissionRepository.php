<?php

declare(strict_types=1);

namespace Modules\Staff\Repositories;

/**
 * Interface StaffPermissionRepository
 * @package Modules\Staff\Repositories
 */
interface StaffPermissionRepository
{
    /**
     * Returns array with permission ids.
     * @return int[]
     */
    public function getIdsByPolicyNames(array $permissionsByPolicyNames): array;

    /**
     * Returns all data of permissions.
     * @return int[]
     */
    public function getAllIds(): array;

    /**
     * Returns data about all permissions.
     */
    public function getAll(): array;
}
