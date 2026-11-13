<?php

declare(strict_types=1);

namespace Modules\Currency\Repositories;

/**
 * Interface CurrencyRepository
 * @package Modules\Currency\Repositories
 */
interface CurrencyRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates currency data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new currency.
     */
    public function create(array $data): int;

    /**
     * Deletes the currency.
     */
    public function delete(): self;

    /**
     * Returns currency data.
     */
    public function data(): array;

    /**
     * Returns data of all currencies.
     */
    public function all(): array;

    /**
     * Returns id currency by country code.
     *
     * @param  string  $code Country code
     */
    public function getIdByCode(string $code): int;
}
