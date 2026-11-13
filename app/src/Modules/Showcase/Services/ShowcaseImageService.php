<?php

declare(strict_types=1);

namespace Modules\Showcase\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class ShowcaseImageService
{
    private string $disk;

    private string $directory;

    public function __construct()
    {
        $this->disk = (string) Config::get('showcase.disk', 'public');
        $this->directory = (string) Config::get('showcase.images_upload_dir', 'showcase/images');
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

        return Storage::disk($this->disk)->url($relativePath);
    }
}
