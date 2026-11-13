<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories;

/**
 * Interface BrandRepository
 * @package Modules\Catalog\Repositories
 */
interface BrandRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates brand data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new brand.
     */
    public function create(array $data): int;

    /**
     * Deletes the brand.
     */
    public function delete(): self;

    /**
     * Returns brand data.
     */
    public function data(): array;

    /**
     * Returns data of all brands.
     */
    public function all(bool $isHidden, ?string $search): array;
}
