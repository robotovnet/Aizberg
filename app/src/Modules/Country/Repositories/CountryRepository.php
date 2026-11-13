<?php

declare(strict_types=1);

namespace Modules\Country\Repositories;

/**
 * Interface CountryRepository
 * @package Modules\Country\Repositories
 */
interface CountryRepository
{
    /**
     * Returns id country by country code.
     */
    public function getIdByCode(string $code): int;

    /**
     * Returns language code by country code.
     */
    public function getLanguageByCode(string $code): string;

    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates country data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new country.
     */
    public function create(array $data): int;

    /**
     * Deletes the country.
     */
    public function delete(): self;

    /**
     * Returns country data.
     */
    public function data(): array;

    /**
     * Returns data of all countries.
     */
    public function all(string $withcities, ?string $search): array;
}
