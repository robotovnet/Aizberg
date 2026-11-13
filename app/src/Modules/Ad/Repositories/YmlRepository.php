<?php

declare(strict_types=1);

namespace Modules\Ad\Repositories;

/**
 * Interface YmlRepository
 * @package Modules\Ad\Repositories
 */
interface YmlRepository
{
    /**
     * Sets the yml.
     */
    public function setModel(int $id): self;

    /**
     * Updates yml data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new yml.
     */
    public function create(array $data): int;

    /**
     * Deletes the yml.
     */
    public function delete(): self;

    /**
     * Returns yml data.
     */
    public function data(): array;

    /**
     * Returns data of all ymls.
     */
    public function all(): array;
}
