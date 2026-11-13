<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories;

/**
 * Interface ProductRepository
 * @package Modules\Catalog\Repositories
 */
interface ProductRepository
{
    /**
     * Sets the model.
     */
    public function setModel(int $id): self;

    /**
     * Updates product data.
     */
    public function update(array $attributes): self;

    /**
     * Creates a new product.
     */
    public function create(array $data): int;

    /**
     * Deletes the product.
     */
    public function delete(): self;

    /**
     * Returns product data.
     */
    public function data(): array;

    /**
     * Returns a list of similar products.
     */
    public function getSimilarProducts(array $products): array;

    public function addSimilarProduct(int $productId, int $similarId): bool;
    public function deleteSimilarProduct(int $productId, int $similarId): void;

    /**
     * Returns the number of products in the catalog section.
     *  @param  int  $sectionId ID of the catalog section.
     *  @param  string  $sorting    Sets the order in which products are displayed.
     */
    public function getQtyProductsInCat(int $sectionId, string $sorting = 'by_price'): int;

    /**
     * Returns data of all products.
     * @param  string  $search     Search query for finding products by name or article.
     * @param  int     $page       Page sequence number.
     * @param  int     $sectionId  ID of the catalog section.
     * @param  string  $sorting    Sets the order in which products are displayed.
     * @param  string  $direction  Sets the sorting direction (asc | desc)
     * @param  array   $filters    Sets the conditions that the returned products must meet.
     * @param  int     $onPage     The number of products displayed on one page.
     * @param  array   $productIDs List of product ids
     */
    public function all(
        ?string $search,
        int $page,
        int $sectionId,
        string $sorting,
        string $direction,
        array $filters,
        int $onPage,
        ?array $productIDs
    ): array|int;

    /**
     * Toggle visibility of product
     * @param      int   $pid    Product id
     * @return     bool
     */
    public function toggleVisibilityOfProduct(int $pid): bool;

    /**
     * Получает описание товара по артикулу.
     *
     * @param string $article Артикул товара
     * @return string|null Описание товара или null, если товар не найден
     */
    public function getDescriptionByArticle(string $article): string|null;
}
