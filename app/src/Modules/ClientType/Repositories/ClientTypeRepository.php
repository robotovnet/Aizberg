<?php

declare(strict_types=1);

namespace Modules\ClientType\Repositories;

/**
 * Interface ClientTypeRepository
 * @package Modules\ClientType\Repositories
 */
interface ClientTypeRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates client type data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new client type.
     */
    public function create(array $data): int;

    /**
     * Deletes the client type.
     */
    public function delete(): self;

    /**
     * Returns client type data.
     */
    public function data(): array;

    /**
     * Returns data of all client types.
     */
    public function all(): array;
}
