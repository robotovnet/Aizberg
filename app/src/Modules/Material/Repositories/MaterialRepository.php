<?php

declare(strict_types=1);

namespace Modules\Material\Repositories;

/**
 * Interface MaterialRepository
 * @package Modules\Material\Repositories
 */
interface MaterialRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates material data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new material.
     */
    public function create(array $data): int;

    /**
     * Deletes the material.
     */
    public function delete(): self;

    /**
     * Returns material data.
     */
    public function data(): array;

    /**
     * Finds the page by slug and shop.
     */
    public function findPage(int $shopId, string $slug): ?array;

    /**
     * Returns data of all materials.
     */
    public function all(?string $search): array;

    /**
     * Returns all sections of materials.
     */
    public function sections(): array;
}
