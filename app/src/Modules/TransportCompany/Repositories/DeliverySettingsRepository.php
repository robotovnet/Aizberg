<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories;

/**
 * Interface DeliverySettingsRepository
 * @package Modules\TransportCompany\Repositories
 */
interface DeliverySettingsRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates delivery settings.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new delivery settings.
     */
    public function create(array $data): int;

    /**
     * Deletes the delivery settings.
     */
    public function delete(): self;

    /**
     * Returns delivery settings.
     */
    public function data(): array;
}
