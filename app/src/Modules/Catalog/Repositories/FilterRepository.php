<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories;

/**
 * Interface FilterRepository
 * @package Modules\Catalog\Repositories
 */
interface FilterRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates filter data.
     */
    public function update(array $attributes): self;

    /**
     * Updates many filters data.
     */
    public function updateMany(array $data): self;

    /**
     * Creates a new filter.
     */
    public function create(array $data): array;

    /**
     * Deletes the filter.
     */
    public function delete(): self;

    /**
     * Returns filter data.
     */
    public function data(): array;

    /**
     * Returns data of all filters.
     */
    public function all(): array;

    /**
     * Returns the number of filters for each product group.
     */
    public function totalByProductGroup(): array;

    /**
     * Returns all filters by product group
     */
    public function getAll(int $productGroupId, string $langCode): array;

    /**
     * Returns all filter values by product group
     */
    public function getFiltersValues(int $sectionId, array $filtersId, bool $hasPrice = true, int $priceTypeId = 1): array;

    /**
     * Deletes all filters by product group.
     */
    public function deleteAllFiltersByGroup(int $groupId): self;
}
