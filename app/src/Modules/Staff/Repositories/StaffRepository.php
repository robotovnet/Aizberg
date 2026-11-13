<?php

declare(strict_types=1);

namespace Modules\Staff\Repositories;

/**
 * Interface StaffRepository
 * @package Modules\Staff\Repositories
 */
interface StaffRepository
{
    /**
     * Update the model in the database.
     */
    public function update(array $attributes = []): self;

    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Creates a new staff.
     */
    public function create(array $data): self;

    /**
     * Adds the groups the staff belongs to.
     */
    public function addGroups(array $groups): self;

    /**
     * Syncs the groups the staff belongs to.
     */
    public function syncGroups(array $groups): self;

    /**
     * Blocks the staff.
     */
    public function block(): self;

    /**
     * Unblocks the staff.
     */
    public function unblock(): self;

    /**
     * Returns staff profile data.
     */
    public function profile(): array;

    /**
     * Returns the profile data of all staff added to the db.
     */
    public function getAll(int $groupId): array;
}
