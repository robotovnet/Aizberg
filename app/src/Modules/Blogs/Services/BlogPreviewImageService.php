<?php

declare(strict_types=1);

namespace Modules\Blogs\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class BlogPreviewImageService
{
    private string $disk;

    private string $directory;

    private ?string $apiHost;

    public function __construct()
    {
        $this->disk = (string) Config::get('blogs.disk', 'public');
        $this->directory = (string) Config::get('blogs.preview_upload_dir', 'blogs/previews');
        $apiHost = trim((string) Config::get('blogs.api_host', ''));
        $this->apiHost = $apiHost !== '' ? $apiHost : null;
    }

    public function store(UploadedFile $file): string
    {
        return $file->store($this->directory, $this->disk);
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
}
