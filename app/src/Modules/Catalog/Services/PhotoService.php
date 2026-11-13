<?php

declare(strict_types=1);

namespace Modules\Catalog\Services;

use Modules\Staff\Services\UserpicService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * Class PhotoService
 * @package Modules\Catalog\Services
 */
class PhotoService extends UserpicService
{
    /** @var string Folder path where mini photos are uploaded. */
    protected string $uploadDirMini;

    /**
     * PhotoService constructor.
     */
    public function __construct(UploadedFile $file)
    {
        parent::__construct($file);

        $moduleName = 'catalog';
        $this->uploadDir       = Config::get($moduleName . '.upload_micro_photo_dir');
        $this->uploadDirMini   = Config::get($moduleName . '.upload_mini_photo_dir');
        $this->uploadDirOrigin = Config::get($moduleName . '.upload_origin_photo_dir');
    }

    public function save(): void
    {
        // Сохраняем оригинал
        Storage::disk($this->disk)->putFileAs($this->uploadDirOrigin, $this->file, $this->fileName);
        
        // Создаем mini версию (средний размер)
        $this->resizeAndSave(
            $this->uploadDirMini,
            (int)Config::get('catalog.mini_photo_width'),
            (int)Config::get('catalog.mini_photo_height')
        );
        
        // Создаем micro версию (самый маленький размер)
        $this->resizeAndSave(
            $this->uploadDir,
            (int)Config::get('catalog.photo_width'),
            (int)Config::get('catalog.photo_height')
        );
    }

    /**
     * Ресайзит изображение и сохраняет в указанную директорию
     * 
     * @param string $targetDir Директория для сохранения
     * @param int $width Ширина изображения
     * @param int $height Высота изображения
     */
    private function resizeAndSave(string $targetDir, int $width, int $height): void
    {
        $uploadedFile = $this->file->getRealPath();

        list($originalWidth, $originalHeight) = getimagesize($uploadedFile);

        $aspectRatio = $originalWidth / $originalHeight;
        
        if ($width / $height > $aspectRatio) {
            $newwidth = $height * $aspectRatio;
            $newheight = $height;
        } else {
            $newheight = $width / $aspectRatio;
            $newwidth = $width;
        }

        $ext = strtolower($this->file->getClientOriginalExtension());

        if ($ext == 'jpg' || $ext == 'jpeg') {
            $src = $this->imagecreatefromjpegexif($uploadedFile);
        } elseif ($ext == 'png') {
            // Подавляем предупреждение libpng о некорректном iCCP профиле
            $src = @imagecreatefrompng($uploadedFile);
            if ($src === false) {
                // Если не удалось создать из PNG, пробуем через string
                $src = @imagecreatefromstring(file_get_contents($uploadedFile));
            }
        } else {
            // Подавляем предупреждение libpng о некорректном iCCP профиле
            $src = @imagecreatefromstring(file_get_contents($uploadedFile));
        }

        $dst = imagecreatetruecolor((int)$newwidth, (int)$newheight);

        if ($ext == 'png') {
            // Отключаем режим сопряжения цветов
            imagealphablending($dst, false);
            // Включаем сохранение альфа канала
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, (int)$newwidth, (int)$newheight, (int)$originalWidth, (int)$originalHeight);

        $targetPath = storage_path('app/public/' . $targetDir . '/' . $this->fileName);

        if ($ext == 'png') {
            imagepng($dst, $targetPath);
        } else {
            imagejpeg($dst, $targetPath, 90);
        }

        imagedestroy($dst);
        imagedestroy($src);
    }

    /**
     * Создает изображение из JPEG с учетом EXIF ориентации
     * 
     * @param string $filename
     * @return false|\GdImage
     */
    private function imagecreatefromjpegexif(string $filename): false|\GdImage
    {
        $img = imagecreatefromjpeg($filename);
        $exif = exif_read_data($filename);

        if ($img && $exif && isset($exif['Orientation'])) {
            $ort = $exif['Orientation'];

            if ($ort == 6 || $ort == 5) {
                $img = imagerotate($img, 270, 0);
            }

            if ($ort == 3 || $ort == 4) {
                $img = imagerotate($img, 180, 0);
            }

            if ($ort == 8 || $ort == 7) {
                $img = imagerotate($img, 90, 0);
            }

            if ($ort == 5 || $ort == 4 || $ort == 7) {
                imageflip($img, IMG_FLIP_HORIZONTAL);
            }
        }

        return $img;
    }
}
