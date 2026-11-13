<?php

declare(strict_types=1);

namespace Modules\Catalog\Console;

use Illuminate\Console\Command;
use Modules\Catalog\Entities\ProductPhoto;
use Modules\Catalog\Support\ProductPhotoOriginPath;

class BackfillProductPhotoContentHashCommand extends Command
{
    protected $signature = 'catalog:product-photos-backfill-content-hash
                            {--force : Пересчитать content_hash даже если значение уже задано}';

    protected $description = 'Рассчитать content_hash (SHA-256 файла оригинала на диске) для записей product_photos';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $updated = 0;
        $skippedMissing = 0;

        ProductPhoto::query()
            ->when(! $force, fn ($q) => $q->whereNull('content_hash'))
            ->orderBy('id')
            ->chunkById(200, function ($photos) use (&$updated, &$skippedMissing) {
                foreach ($photos as $photo) {
                    /** @var ProductPhoto $photo */
                    $absolute = ProductPhotoOriginPath::absoluteOriginFromStoredPath($photo->file_path);
                    if (! is_readable($absolute)) {
                        $this->warn("Файл не найден или недоступен (id={$photo->id}): {$absolute}");
                        $skippedMissing++;

                        continue;
                    }

                    $hash = hash_file('sha256', $absolute);
                    if ($hash === false) {
                        $this->warn("Не удалось хешировать id={$photo->id}: {$absolute}");
                        $skippedMissing++;

                        continue;
                    }

                    $photo->content_hash = $hash;
                    $photo->saveQuietly();
                    $updated++;
                }
            });

        $this->info("Обновлено записей: {$updated}");
        $this->info("Пропущено (нет файла / ошибка чтения): {$skippedMissing}");

        return self::SUCCESS;
    }
}
