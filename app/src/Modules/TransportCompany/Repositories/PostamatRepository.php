<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Repositories;

/**
 * Interface PostamatRepository
 * @package Modules\TransportCompany\Repositories
 */
interface PostamatRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates postamat.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new postamat.
     */
    public function create(array $data): int;

    /**
     * Returns data of all postamats.
     * @param  int  $page Page sequence number. Starts with one.
     * @param  int  $totalPerPage How many maximum objects should be returned.
     * @param  string  $addingMethod Possible values: any, preset, manual.
     * @param  int  $city City id.
     * @param  int  $tcId Transport company id.
     * @return array
     */
    public function all(int $page, int $totalPerPage, string $addingMethod, int $city, int $tcId): array;

    /**
     * Deletes the postamat.
     */
    public function delete(): self;

    /**
     * Returns data of postamat.
     */
    public function data(): array;

    /**
     * Returns the number of postamats in the database.
     */
    public function total(string $addingMethod, int $city, int $tcId): int;

    /**
     * Returns a list of cities that have postamates.
     */
    public function cities(): array;
}
