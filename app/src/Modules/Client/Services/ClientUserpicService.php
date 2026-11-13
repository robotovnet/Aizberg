<?php

declare(strict_types=1);

namespace Modules\Client\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\ClientUserpic;
use RuntimeException;
use Throwable;

class ClientUserpicService
{
    private const MAX_WIDTH = 600;

    private string $disk;
    private string $directory;
    private ?string $apiHost;

    public function __construct()
    {
        $this->disk = (string)Config::get('client.userpic_disk', 'public');
        $this->directory = (string)Config::get('client.userpic_upload_dir', 'clients/userpics');
        $apiHost = trim((string)Config::get('client.api_host', ''));
        $this->apiHost = $apiHost !== '' ? $apiHost : null;
    }

    public function replace(Client $client, UploadedFile $file): ClientUserpic
    {
        [$contents, $mimeType, $extension] = $this->resizeAndEncode($file);
        $newPath = trim($this->directory, '/') . '/' . Str::uuid() . '.' . $extension;

        if (!Storage::disk($this->disk)->put($newPath, $contents)) {
            throw new RuntimeException('Не удалось сохранить userpic.');
        }

        try {
            [$userpic, $oldPath] = DB::transaction(function () use (
                $client, $file, $newPath, $mimeType, $contents
            ): array {
                $current = ClientUserpic::query()
                    ->where('client_id', $client->id)
                    ->lockForUpdate()
                    ->first();
                $oldPath = $current?->path;

                $userpic = $current ?? new ClientUserpic(['client_id' => $client->id]);
                $userpic->fill([
                    'path' => $newPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $mimeType,
                    'size' => strlen($contents),
                ]);
                $userpic->save();

                return [$userpic->fresh(), $oldPath];
            });
        } catch (Throwable $exception) {
            Storage::disk($this->disk)->delete($newPath);
            throw $exception;
        }

        if ($oldPath !== null && $oldPath !== $newPath) {
            Storage::disk($this->disk)->delete($oldPath);
        }

        return $userpic;
    }

    /** @return array{0: string, 1: string, 2: string} */
    private function resizeAndEncode(UploadedFile $file): array
    {
        $sourceContents = file_get_contents($file->getRealPath());
        $source = is_string($sourceContents) ? imagecreatefromstring($sourceContents) : false;
        if ($source === false) {
            throw new RuntimeException('Не удалось прочитать изображение userpic.');
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $targetWidth = min(self::MAX_WIDTH, $sourceWidth);
        $targetHeight = max(1, (int)round($sourceHeight * ($targetWidth / $sourceWidth)));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($target === false) {
            imagedestroy($source);
            throw new RuntimeException('Не удалось подготовить изображение userpic.');
        }

        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled(
            $target, $source, 0, 0, 0, 0,
            $targetWidth, $targetHeight, $sourceWidth, $sourceHeight
        );

        $sourceMime = (string)($file->getMimeType() ?: $file->getClientMimeType());
        [$mimeType, $extension] = $this->outputFormat($sourceMime);

        if ($mimeType === 'image/gif') {
            imagecolortransparent($target, $transparent);
        }

        ob_start();
        $encoded = match ($mimeType) {
            'image/jpeg' => imagejpeg($target, null, 90),
            'image/png' => imagepng($target, null, 6),
            'image/gif' => imagegif($target),
            'image/webp' => imagewebp($target, null, 90),
        };
        $contents = ob_get_clean();

        imagedestroy($target);
        imagedestroy($source);

        if (!$encoded || !is_string($contents) || $contents === '') {
            throw new RuntimeException('Не удалось сжать изображение userpic.');
        }

        return [$contents, $mimeType, $extension];
    }

    /** @return array{0: string, 1: string} */
    private function outputFormat(string $mimeType): array
    {
        return match ($mimeType) {
            'image/jpeg' => ['image/jpeg', 'jpg'],
            'image/png' => ['image/png', 'png'],
            'image/gif' => ['image/gif', 'gif'],
            'image/webp' => ['image/webp', 'webp'],
            default => throw new RuntimeException('Неподдерживаемый формат изображения userpic.'),
        };
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
}
