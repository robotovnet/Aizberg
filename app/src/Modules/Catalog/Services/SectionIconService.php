<?php

declare(strict_types=1);

namespace Modules\Catalog\Services;

use Modules\Staff\Services\UserpicService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * Class SectionIconService
 * @package Modules\Catalog\Services
 */
class SectionIconService extends UserpicService
{
    /**
     * IconService constructor.
     */
    public function __construct(UploadedFile $file)
    {
        parent::__construct($file);

        $moduleName = 'catalog';
        $this->uploadDir = Config::get($moduleName . '.upload_dir');
    }

    /**
     * Saves the file to disk.
     * For SVG files, saves without resizing. For raster images, uses parent method.
     */
    public function save(): void
    {
        // Для SVG просто сохраняем файл без изменения размера
        if ($this->isSvgFile()) {
            Storage::disk($this->disk)->putFileAs($this->uploadDir, $this->file, $this->fileName);
        } else {
            // Для растровых изображений (jpg, png) используем метод изменения размера
            parent::save();
        }
    }

    /**
     * Определяет, является ли файл SVG
     * Проверяет расширение, MIME-тип и содержимое файла
     */
    private function isSvgFile(): bool
    {
        // 1. Проверка по расширению файла
        $extension = strtolower($this->file->getClientOriginalExtension());
        if ($extension === 'svg') {
            return true;
        }

        // 2. Проверка MIME-типа
        $mimeType = $this->file->getMimeType();
        if (in_array($mimeType, ['image/svg+xml', 'image/svg', 'text/xml', 'application/xml'])) {
            return true;
        }

        // 3. Проверка содержимого файла (первые несколько байт)
        try {
            $content = file_get_contents($this->file->getPathname());
            if ($content !== false) {
                // Проверяем, содержит ли файл SVG-теги
                $svgSignature = strtolower(trim(substr($content, 0, 100)));
                if (strpos($svgSignature, '<svg') !== false || strpos($svgSignature, '<?xml') !== false) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            // Если не удалось прочитать файл, продолжаем с другими проверками
        }

        return false;
    }
}
