<?php

declare(strict_types=1);

namespace Modules\Lottery\Repositories;

/**
 * Interface LotteryAppsRepository
 * @package Modules\Lottery\Repositories
 */
interface LotteryAppsRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Returns data of all lottery apps.
     */
    public function all(string $orderBy = 'id'): array;

    /**
     * Returns data of a lottery app.
     */
    public function detail(int $id): array;

    /**
     * Accepts a lottery app.
     */
    public function accept(int $id): void;

    /**
     * Rejects a lottery app.
     */
    public function reject(int $id, ?string $rejectDesc = ''): void;

    /**
     * Returns total count of lottery apps.
     */
    public function total(): int;

    /**
     * Registers a lottery application.
     */
    public function register(array $data): bool;
}
