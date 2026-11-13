<?php

declare(strict_types=1);

namespace Modules\ServiceCenter\Repositories;

/**
 * Interface ServiceCenterRepository
 * @package Modules\ServiceCenter\Repositories
 */
interface ServiceCenterRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates pickup point.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new pickup point.
     */
    public function create(array $data): int;

    /**
     * Returns data of all pickup points.
     * @param  int  $page Page sequence number. Starts with one.
     * @param  int  $totalPerPage How many maximum objects should be returned.
     * @param  int  $city City id.
     * @return array
     */
    public function all(int $page, int $totalPerPage, int $city): array;

    /**
     * Deletes the pickup point.
     */
    public function delete(): self;

    /**
     * Returns data of pickup point.
     */
    public function data(): array;

    /**
     * Returns the number of pickup points in the database.
     */
    public function total(int $city): int;

    /**
     * Returns a list of cities that have postamates.
     */
    public function cities(): array;
}
