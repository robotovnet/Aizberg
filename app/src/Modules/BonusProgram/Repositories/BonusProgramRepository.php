<?php

declare(strict_types=1);

namespace Modules\BonusProgram\Repositories;

/**
 * Interface BonusProgramRepository
 * @package Modules\BonusProgram\Repositories
 */
interface BonusProgramRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates bonus level data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new bonus level.
     */
    public function create(array $data): int;

    /**
     * Deletes the bonus level.
     */
    public function delete(): self;

    /**
     * Returns bonus level data.
     */
    public function data(): array;

    /**
     * Returns data of all bonus levels.
     */
    public function all(): array;
}
