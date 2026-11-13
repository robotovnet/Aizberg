<?php

declare(strict_types=1);

namespace Modules\Shop\Repositories;

/**
 * Interface ShopRepository
 * @package Modules\Shop\Repositories
 */
interface ShopRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates shop data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new shop.
     */
    public function create(array $data): int;

    /**
     * Deletes the shop.
     */
    public function delete(): self;

    /**
     * Returns the shop data.
     */
    public function data(): array;

    /**
     * Returns data of all shops.
     */
    public function all(): array;
}
