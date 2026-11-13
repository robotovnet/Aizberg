<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class MarketplaceModuleIconService
{
    private string $disk;

    private string $directory;

    private int $width;

    private int $height;

    private ?string $apiHost;

    public function __construct()
    {
        $this->disk = (string)Config::get('marketplacemodules.disk', 'public');
        $this->directory = (string)Config::get('marketplacemodules.icon_upload_dir', 'marketplace-modules/icons');
        $this->width = max(1, (int)Config::get('marketplacemodules.icon_width', 112));
        $this->height = max(1, (int)Config::get('marketplacemodules.icon_height', 112));
        $apiHost = trim((string)Config::get('marketplacemodules.api_host', ''));
        $this->apiHost = $apiHost !== '' ? $apiHost : null;
    }

    /**
     * Уменьшает иконку до размера из конфига, сохраняет и возвращает относительный путь
     * в хранилище (для колонки icon_path). SVG сохраняется без изменений.
     */
    public function store(UploadedFile $file): string
    {
        $sourceMime = (string)($file->getMimeType() ?: $file->getClientMimeType());
        if ($sourceMime === 'image/svg+xml') {
            return $file->store($this->directory, $this->disk);
        }

        $extension = $this->outputExtension($sourceMime);
        $contents = $this->resizeAndEncode((string)file_get_contents($file->getRealPath()), $sourceMime);
        $path = trim($this->directory, '/') . '/' . Str::uuid() . '.' . $extension;

        if (!Storage::disk($this->disk)->put($path, $contents)) {
            throw new RuntimeException('Не удалось сохранить иконку модуля.');
        }

        return $path;
    }

    /**
     * Пережимает уже сохранённую иконку на месте (путь и формат не меняются).
     * Возвращает false, если иконка векторная или уже соответствует размеру из конфига.
     */
    public function recompressStored(string $relativePath, bool $dryRun = false): bool
    {
        $disk = Storage::disk($this->disk);
        if (!$disk->exists($relativePath)) {
            throw new RuntimeException('Файл иконки не найден: ' . $relativePath);
        }

        $contents = (string)$disk->get($relativePath);
        $info = getimagesizefromstring($contents);
        if ($info === false) {
            if (str_ends_with(strtolower($relativePath), '.svg')) {
                return false;
            }
            throw new RuntimeException('Не удалось прочитать изображение иконки: ' . $relativePath);
        }

        [, , , , $targetWidth, $targetHeight] = $this->geometry($info[0], $info[1]);
        if ($targetWidth === $info[0] && $targetHeight === $info[1]) {
            return false;
        }

        if ($dryRun) {
            return true;
        }

        if (!$disk->put($relativePath, $this->resizeAndEncode($contents, (string)$info['mime']))) {
            throw new RuntimeException('Не удалось сохранить иконку: ' . $relativePath);
        }

        return true;
    }

    public function deleteByStoredPath(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        if (Storage::disk($this->disk)->exists($relativePath)) {
            Storage::disk($this->disk)->delete($relativePath);
        }
    }

    public function publicUrl(?string $relativePath): ?string
    {
        if ($relativePath === null || $relativePath === '') {
            return null;
        }

        if ($this->apiHost !== null) {
            $baseUrl = preg_match('#^https?://#i', $this->apiHost) === 1
                ? $this->apiHost
                : 'https://' . $this->apiHost;

            return rtrim($baseUrl, '/') . '/storage/' . ltrim($relativePath, '/');
        }

        return Storage::disk($this->disk)->url($relativePath);
    }

    /**
     * Центрированный crop под пропорции целевого размера и уменьшение до него.
     * Маленькие иконки не растягиваются.
     *
     * @return array{0: int, 1: int, 2: int, 3: int, 4: int, 5: int} cropX, cropY, cropW, cropH, targetW, targetH
     */
    private function geometry(int $sourceWidth, int $sourceHeight): array
    {
        $targetRatio = $this->width / $this->height;
        $cropWidth = $sourceWidth;
        $cropHeight = $sourceHeight;
        if ($sourceWidth / $sourceHeight > $targetRatio) {
            $cropWidth = max(1, (int)round($sourceHeight * $targetRatio));
        } else {
            $cropHeight = max(1, (int)round($sourceWidth / $targetRatio));
        }

        $scale = min(1, $this->width / $cropWidth);

        return [
            intdiv($sourceWidth - $cropWidth, 2),
            intdiv($sourceHeight - $cropHeight, 2),
            $cropWidth,
            $cropHeight,
            max(1, (int)round($cropWidth * $scale)),
            max(1, (int)round($cropHeight * $scale)),
        ];
    }

    private function resizeAndEncode(string $sourceContents, string $sourceMime): string
    {
        // Отсекаем неподдерживаемые форматы до декодирования.
        $this->outputExtension($sourceMime);

        $source = imagecreatefromstring($sourceContents);
        if ($source === false) {
            throw new RuntimeException('Не удалось прочитать изображение иконки модуля.');
        }

        [$cropX, $cropY, $cropWidth, $cropHeight, $targetWidth, $targetHeight] = $this->geometry(
            imagesx($source),
            imagesy($source)
        );

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($target === false) {
            imagedestroy($source);
            throw new RuntimeException('Не удалось подготовить изображение иконки модуля.');
        }

        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled(
            $target, $source, 0, 0, $cropX, $cropY,
            $targetWidth, $targetHeight, $cropWidth, $cropHeight
        );

        if ($sourceMime === 'image/gif') {
            imagecolortransparent($target, $transparent);
        }

        ob_start();
        $encoded = match ($sourceMime) {
            'image/jpeg' => imagejpeg($target, null, 90),
            'image/png' => imagepng($target, null, 6),
            'image/gif' => imagegif($target),
            'image/webp' => imagewebp($target, null, 90),
        };
        $contents = ob_get_clean();

        imagedestroy($target);
        imagedestroy($source);

        if (!$encoded || !is_string($contents) || $contents === '') {
            throw new RuntimeException('Не удалось сжать изображение иконки модуля.');
        }

        return $contents;
    }

    private function outputExtension(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => throw new RuntimeException('Неподдерживаемый формат изображения иконки модуля.'),
        };
    }
}
