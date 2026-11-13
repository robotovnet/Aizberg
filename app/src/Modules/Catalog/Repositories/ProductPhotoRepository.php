<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Interface ProductPhotoRepository
 * @package Modules\Catalog\Repositories
 */
interface ProductPhotoRepository
{
    /**
     *  Creates a new record of photo.
     */
    public function create(string $path, string $uuid, int $order, string $main, string $contentHash): int;

    /**
     * Есть ли среди не удалённых фото товара такое же содержимое файла (по SHA-256 оригинала).
     */
    public function hasActiveDuplicateByUuidAndContentHash(string $uuid, string $contentHash): bool;

    /**
     * Deletes the record of photo.
     */
    public function delete(int $photoId): self;

    /**
     * Download all photos of the product.
     */
    public function downloadPhotos(int $productId): BinaryFileResponse;
}
