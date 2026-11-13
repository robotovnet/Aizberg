<?php

declare(strict_types=1);

namespace Modules\RealProjects\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Хранение баннеров проектов. Файлы (включая SVG) сохраняются без изменений.
 */
class RealProjectBannerService
{
    private string $disk;

    private string $directory;

    private ?string $apiHost;

    public function __construct()
    {
        $this->disk = (string)Config::get('real_projects.disk', 'public');
        $this->directory = trim((string)Config::get('real_projects.banner_upload_dir', 'real-projects'), '/');
        $apiHost = trim((string)Config::get('real_projects.api_host', ''));
        $this->apiHost = $apiHost !== '' ? $apiHost : null;
    }

    /**
     * Сохраняет баннер и возвращает путь относительно диска (для колонки banner_path).
     */
    public function store(UploadedFile $file): string
    {
        $path = $file->store($this->directory, $this->disk);

        if ($path === false) {
            throw new RuntimeException('Не удалось сохранить баннер проекта.');
        }

        return $path;
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

    /**
     * Публичный URL баннера: https://{API_HOST}/storage/real-projects/abc.svg.
     * Без API_HOST — путь относительно хоста API: /storage/real-projects/abc.svg
     */
    public function publicUrl(?string $relativePath): ?string
    {
        if ($relativePath === null || $relativePath === '') {
            return null;
        }

        $path = '/storage/' . ltrim($relativePath, '/');

        if ($this->apiHost === null) {
            return $path;
        }

        $baseUrl = preg_match('#^https?://#i', $this->apiHost) === 1
            ? $this->apiHost
            : 'https://' . $this->apiHost;

        return rtrim($baseUrl, '/') . $path;
    }
}
