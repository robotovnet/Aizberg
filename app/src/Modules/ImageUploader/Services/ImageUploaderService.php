<?php

declare(strict_types=1);

namespace Modules\ImageUploader\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\ImageUploader\Entities\ImageUpload;
use RuntimeException;

class ImageUploaderService
{
    public function upload(UploadedFile $file, string $title, ?string $category = null): ImageUpload
    {
        $category = $this->normalizeCategory($category);
        $disk = $this->getDisk();
        $directory = $this->getDirectory();
        $datePath = date('Y/m/d');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $baseName = Str::uuid()->toString();
        $filename = $baseName . '.' . $extension;
        $originalPath = $this->normalizePath($directory . '/' . $datePath . '/' . $filename);
        $microPath = $this->normalizePath($directory . '/' . $datePath . '/' . $baseName . '_micro.' . $extension);

        $rawContents = $file->get();
        Storage::disk($disk)->put($originalPath, $rawContents);

        $microContents = $this->makeMicroCopy($rawContents, $extension);
        Storage::disk($disk)->put($microPath, $microContents);

        return ImageUpload::query()->create([
            'title' => $title,
            'category' => $category,
            'original_path' => $originalPath,
            'micro_path' => $microPath,
        ]);
    }

    public function list(?string $category, int $page): LengthAwarePaginator
    {
        $perPage = max(1, (int) config('imageuploader.per_page', 24));

        $query = ImageUpload::query()
            ->when($category !== null && $category !== '', static function ($builder) use ($category): void {
                $builder->where('category', $category);
            })
            ->orderByDesc('created_at');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function find(int $id): ImageUpload
    {
        return ImageUpload::query()->findOrFail($id);
    }

    public function updateInfo(ImageUpload $image, string $title, ?string $category = null): ImageUpload
    {
        $category = $this->normalizeCategory($category);
        $image->fill([
            'title' => $title,
            'category' => $category,
        ]);
        $image->save();

        return $image;
    }

    public function categories(): array
    {
        return ImageUpload::query()
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->filter(static fn (?string $category) => $category !== null && $category !== '')
            ->values()
            ->all();
    }

    public function delete(ImageUpload $image): void
    {
        $disk = $this->getDisk();

        if ($image->original_path) {
            Storage::disk($disk)->delete($image->original_path);
        }

        if ($image->micro_path && $image->micro_path !== $image->original_path) {
            Storage::disk($disk)->delete($image->micro_path);
        }

        $image->delete();
    }

    public function urls(ImageUpload $image): array
    {
        $disk = $this->getDisk();

        $originalUrl = Storage::disk($disk)->url($image->original_path);
        $microUrl = $image->micro_path ? Storage::disk($disk)->url($image->micro_path) : null;

        return [
            'original' => $this->convertToApiSubdomain($originalUrl),
            'micro' => $microUrl ? $this->convertToApiSubdomain($microUrl) : null,
        ];
    }

    private function convertToApiSubdomain(string $url): string
    {
        $parsed = parse_url($url);
        
        if (!isset($parsed['host'])) {
            return $url;
        }

        $host = $parsed['host'];
        
        // Если уже есть поддомен api, возвращаем как есть
        if (str_starts_with($host, 'api.')) {
            return $url;
        }

        // Заменяем домен на поддомен api
        $newHost = 'api.' . $host;
        $parsed['host'] = $newHost;

        $scheme = $parsed['scheme'] ?? 'http';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $path = $parsed['path'] ?? '';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

        return $scheme . '://' . $newHost . $port . $path . $query . $fragment;
    }

    private function makeMicroCopy(string $contents, string $extension): string
    {
        $targetWidth = max(1, (int) config('imageuploader.micro_width', 240));
        $imageInfo = getimagesizefromstring($contents);

        if ($imageInfo === false) {
            throw new RuntimeException('Не удалось определить размер изображения.');
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'] ?? null;

        if ($width <= $targetWidth) {
            return $contents;
        }

        $ratio = $targetWidth / $width;
        $targetHeight = (int) round($height * $ratio);

        $source = imagecreatefromstring($contents);
        $destination = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($source === false || $destination === false) {
            throw new RuntimeException('Не удалось обработать изображение.');
        }

        $this->prepareTransparency($source, $destination, $mime);

        imagecopyresampled(
            $destination,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height
        );

        ob_start();

        $this->outputImageResource($destination, $mime, $extension);

        $resized = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($destination);

        return $resized;
    }

    private function outputImageResource($resource, ?string $mime, string $extension): void
    {
        $extension = strtolower($extension);
        $mime = $mime ?? $this->guessMimeByExtension($extension);

        switch ($mime) {
            case 'image/png':
                imagepng($resource);
                break;
            case 'image/gif':
                imagegif($resource);
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    imagewebp($resource);
                    break;
                }
                // fallback to jpeg if webp not supported
            case 'image/jpeg':
            case 'image/pjpeg':
            default:
                imagejpeg($resource, null, 90);
                break;
        }
    }

    private function prepareTransparency($source, $destination, ?string $mime): void
    {
        if (in_array($mime, ['image/png', 'image/gif'], true)) {
            imagecolortransparent($destination, imagecolorallocatealpha($destination, 0, 0, 0, 127));
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
        }
    }

    private function guessMimeByExtension(string $extension): string
    {
        return match ($extension) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }

    private function normalizePath(string $path): string
    {
        return ltrim(preg_replace('#/+#', '/', $path), '/');
    }

    private function getDisk(): string
    {
        return (string) config('imageuploader.disk', 'public');
    }

    private function getDirectory(): string
    {
        return trim((string) config('imageuploader.directory', 'images/uploads'), '/');
    }

    private function normalizeCategory(?string $category): ?string
    {
        $category = is_string($category) ? trim($category) : null;

        return $category === '' ? null : $category;
    }
}

