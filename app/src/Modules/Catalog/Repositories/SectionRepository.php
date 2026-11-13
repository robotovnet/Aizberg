<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories;

/**
 * Interface SectionRepository
 * @package Modules\Catalog\Repositories
 */
interface SectionRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates section data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new section.
     */
    public function create(array $data): int;

    /**
     * Deletes the section.
     */
    public function delete(): self;

    /**
     * Returns section data.
     */
    public function data(): array;

    /**
     * Returns the id of the parent section.
     */
    public function getParentSectionId(): int;

    /**
     * Returns data of all sections.
     * @param  bool  $needLevel Returns the nesting levels of categories
     */
    public function all(bool $needLevel): array;

    /**
     * Sets display on main page.
     */
    public function displayOnMainPage(string $shouldDisplay): void;

    /**
     * Sets the list of related products of the catalog section.
     */
    public function relatedProducts(array $products): void;

    /**
     * Allows you to set the order of the catalog sections.
     */
    public function order(array $order): void;

    /**
     * Sets the seo settings for the catalog sections.
     */
    public function seoData(array $data): void;

    /**
     * Deletes data for SEO.
     */
    public function seoDataDelete(): self;

    /**
     * Sets the order of tiled on main page.
     */
    public function orderTiledCatalog(array $order): void;
}
