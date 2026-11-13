<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories;

/**
 * Interface ProductMarkupRepository
 * @package Modules\TransportCompany\Repositories
 */
interface ProductMarkupRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates product markup.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new product markup.
     */
    public function create(array $data): int;

    /**
     * Returns data of all product markups.
     */
    public function all(): array;

    /**
     * Deletes the product markup.
     */
    public function delete(): self;

    /**
     * Returns data of product markup.
     */
    public function data(): array;
}
