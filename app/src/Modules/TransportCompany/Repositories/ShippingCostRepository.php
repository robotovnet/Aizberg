<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories;

/**
 * Interface ShippingCostRepository
 * @package Modules\TransportCompany\Repositories
 */
interface ShippingCostRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates shipping cost.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new shipping cost.
     */
    public function create(array $data): int;

    /**
     * Returns data of all shipping rates.
     */
    public function all(): array;

    /**
     * Deletes the shipping cost.
     */
    public function delete(): self;

    /**
     * Returns data of shipping cost.
     */
    public function data(): array;
}
