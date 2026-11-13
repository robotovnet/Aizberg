<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories;

/**
 * Interface GroupRepository
 * @package Modules\Catalog\Repositories
 */
interface GroupRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates group data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new group.
     */
    public function create(array $data): int;

    /**
     * Deletes the group.
     */
    public function delete(): self;

    /**
     * Returns group data.
     */
    public function data(): array;

    /**
     * Returns data of all groups.
     */
    public function all(?string $search): array;
}
