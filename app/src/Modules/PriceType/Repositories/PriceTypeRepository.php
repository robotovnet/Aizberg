<?php

declare(strict_types=1);

namespace Modules\PriceType\Repositories;

/**
 * Interface PriceTypeRepository
 * @package Modules\PriceType\Repositories
 */
interface PriceTypeRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Update the model in the database.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new price type.
     */
    public function create(array $data): int;

    /**
     * Deletes the price type.
     */
    public function delete(): self;

    /**
     * Returns price type data.
     */
    public function data(): array;

    /**
     * Returns data of all price types.
     */
    public function all(): array;
}
