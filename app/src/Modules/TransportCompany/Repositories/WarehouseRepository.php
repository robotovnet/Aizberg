<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories;

/**
 * Interface WarehouseRepository
 * @package Modules\TransportCompany\Repositories
 */
interface WarehouseRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates warehouse data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new warehouse.
     */
    public function create(array $data): int;

    /**
     * Deletes the warehouse.
     */
    public function delete(): self;

    /**
     * Returns a list of available warehouses in a specific city.
     * @param  int  $cityId City ID
     */
    public function getWarehousesInCity(int $cityId, string $langCode): array;

    /**
     * Returns warehouse data.
     */
    public function data(): array;

    /**
     * Returns data of all warehouses.
     */
    public function all(): array;
}
