<?php

declare(strict_types=1);

namespace Modules\Staff\Repositories;

/**
 * Interface StaffGroupRepository
 * @package Modules\Staff\Repositories
 */
interface StaffGroupRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Creates a new group.
     */
    public function create(array $data): self;

    /**
     * Updates a certain group.
     */
    public function update(array $data): self;

    /**
     *  Creates the permissions granted to the group.
     */
    public function addPermissions(array $permissions): self;

    /**
     *  Get group id.
     */
    public function getId(): int;

    /**
     * Deletes the group.
     */
    public function delete(): self;

    /**
     * Deletes group permissions.
     */
    public function deletePermissions(array $permissionIds): self;

    /**
     * Returns data about all groups.
     */
    public function getAll(): array;

    /**
     * Returns data about certain group.
     */
    public function getCurrent(): array;

    /**
     * Checks for active members in the group.
     */
    public function haveActiveMembers(int $groupId): bool;
}
