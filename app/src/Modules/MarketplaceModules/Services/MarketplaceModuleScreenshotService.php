<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MarketplaceModuleScreenshotService
{
    public const MAX_FILES = 10;

    private string $disk;

    private string $directory;

    private ?string $apiHost;

    public function __construct()
    {
        $this->disk = (string)Config::get('marketplacemodules.screenshot_disk', 'public');
        $this->directory = (string)Config::get(
            'marketplacemodules.screenshot_upload_dir',
            'marketplace-modules/screenshots'
        );
        $apiHost = trim((string)Config::get('marketplacemodules.api_host', ''));
        $this->apiHost = $apiHost !== '' ? $apiHost : null;
    }

    /**
     * @param UploadedFile[] $files
     * @return string[]
     */
    public function storeMany(array $files): array
    {
        $paths = [];

        try {
            foreach ($files as $file) {
                $paths[] = $file->store($this->directory, $this->disk);
            }
        } catch (Throwable $exception) {
            $this->deleteMany($paths);
            throw $exception;
        }

        return $paths;
    }

    /** @param string[] $relativePaths */
    public function deleteMany(array $relativePaths): void
    {
        $paths = array_values(array_filter($relativePaths, static fn ($path) => is_string($path) && $path !== ''));
        if ($paths !== []) {
            Storage::disk($this->disk)->delete($paths);
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
