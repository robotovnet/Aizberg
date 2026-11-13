<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories;

/**
 * Interface CourierCityRepository
 * @package Modules\TransportCompany\Repositories
 */
interface CourierCityRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates courier city.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new courier city.
     */
    public function create(array $data): int;

    /**
     * Returns data of all courier cities.
     * @param  int  $page Page sequence number. Starts with one.
     * @param  int  $totalPerPage How many maximum objects should be returned.
     * @param  string  $addingMethod Possible values: any, preset, manual.
     * @param  int  $tcId Transport company id.
     * @param  int|null  $cityId City id.
     * @param  string  $search Find by city name.
     * @return array
     */
    public function all(int $page, int $totalPerPage, string $addingMethod, int $tcId, ?int $cityId = null, ?string $search = null): array;

    /**
     * Deletes the courier city.
     */
    public function delete(): self;

    /**
     * Returns data of courier city.
     */
    public function data(): array;

    /**
     * Returns the number of courier cities in the database.
     */
    public function total(string $addingMethod, int $tcId): int;
}
