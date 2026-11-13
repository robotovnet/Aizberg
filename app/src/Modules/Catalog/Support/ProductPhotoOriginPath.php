<?php

declare(strict_types=1);

namespace Modules\Catalog\Support;

/**
 * Публичный file_path в БД указывает на micro (или legacy mini); оригинал лежит в origin.
 */
final class ProductPhotoOriginPath
{
    public static function absoluteOriginFromStoredPath(string $storedFilePath): string
    {
        $origin = str_replace(['/micro/', '/mini/'], '/origin/', $storedFilePath);

        return storage_path('app/public') . str_replace('/storage/', '/', $origin);
    }
}
