<?php

declare(strict_types=1);

namespace Modules\City\Repositories;

/**
 * Interface CityRepository
 * @package Modules\City\Repositories
 */
interface CityRepository
{
    /**
     * Returns all cities of a specific country.
     *
     * @param  string  $code
     * @param  int  $page Page sequence number. Starts with one.
     * @return array
     */
    public function getAllByCountryCode(string $code, int $page): array;

    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates city data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new city.
     */
    public function create(array $data): int;

    /**
     * Deletes the city.
     */
    public function delete(): self;

    /**
     * Returns city data.
     */
    public function data(): array;

    /**
     * Returns data of all cities.
     *  @param  int|null  $page Page sequence number. Starts with one.
     */
    public function all(?int $page, ?string $mode, ?string $search): array;

    /**
     * Returns the number of cities in the database.
     */
    public function total(string $countryCode): int;
}
