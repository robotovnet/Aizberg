<?php

declare(strict_types=1);

namespace Modules\Staff\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Nwidart\Modules\Facades\Module;
use GdImage;

/**
 * Class UserpicService
 * @package Modules\Staff\Services
 */
class UserpicService
{
    /** @var UploadedFile Stores the uploaded file. */
    protected UploadedFile $file;

    /** @var string Folder path where userpic are uploaded. */
    protected string $uploadDir;

    /** @var string Folder path where userpic are uploaded. */
    protected string $uploadDirOrigin;

    /** @var string Stores the generated file name. */
    protected string $fileName;

    /** @var string The disk that stores files. */
    protected string $disk;

    /** @var int Resize uploaded files to width. */
    protected int $imageWidth;

    /** @var int Resize uploaded files to height. */
    protected int $imageHeight;

    /** @var bool Indicates whether uploaded files will be cropped */
    protected bool $imageCrop;

    /**
     * UserpicService constructor.
     */
    public function __construct(UploadedFile $file)
    {
        $moduleName = Module::getModuleName(__NAMESPACE__);

        $this->uploadDir = Config::get($moduleName . '.upload_dir');
        $this->disk      = Config::get($moduleName . '.disk');

        $this->imageWidth  = (int)Config::get($moduleName . '.image_width');
        $this->imageHeight = (int)Config::get($moduleName . '.image_height');
        $this->imageCrop   = (bool)Config::get($moduleName . '.image_crop');

        $this->file      = $file;

        $this->getFileName();
    }

    /**
     * Saves the file to disk.
     */
    public function save(): void
    {
        //Storage::disk($this->disk)->putFileAs($this->uploadDir, $this->file, $this->fileName);
        $this->resizeImage();
    }

    /**
     * Returns the path to the file.
     */
    public function getPathToFile(): string
    {
        return Storage::url($this->uploadDir . '/' . $this->fileName);
    }

    /**
     * Returns the file name.
     */
    protected function getFileName(): void
    {
        $filename      = $this->file->getClientOriginalName();
        $fileExtension = $this->file->getClientOriginalExtension();

        $this->fileName = md5($filename . microtime()) . '.' . $fileExtension;
    }

    /**
     * Resizes image and saves to disk
     */
    protected function resizeImage(): void
    {
        $key = 'userpic';

        // TODO refactor this code
        if (isset($_FILES['icon'])) {
            $key = 'icon';
        }

        // TODO refactor this code
        if (isset($_FILES['adphoto'])) {
            $key = 'adphoto';

            $this->imageWidth  = (int)Config::get('catalog.ad_photo_width');
            $this->imageHeight = (int)Config::get('catalog.ad_photo_height');
        }

        // TODO refactor this code
        if (isset($_FILES['photo'])) {
            $key = 'photo';

            $this->imageWidth  = (int)Config::get('catalog.photo_width');
            $this->imageHeight = (int)Config::get('catalog.photo_height');
        }

        $uploadedFile = $_FILES[$key]['tmp_name'];

        $w    = $this->imageWidth;
        $h    = $this->imageHeight;
        $crop = $this->imageCrop;

        list($width, $height) = getimagesize($uploadedFile);

        $aspectRatio = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = abs(ceil($width - ($width * abs($aspectRatio - $w / $h))));
            } else {
                $height = abs(ceil($height - ($height * abs($aspectRatio - $w / $h))));
            }
            $newwidth = $w;
            $newheight = $h;
        } elseif ($w / $h > $aspectRatio) {
            $newwidth = $h * $aspectRatio;
            $newheight = $h;
        } else {
            $newheight = $w / $aspectRatio;
            $newwidth = $w;
        }

        $ext = strtolower($this->file->getClientOriginalExtension());

        if ($ext == 'jpg' || $ext == 'jpeg') {
            $src = $this->imagecreatefromjpegexif($uploadedFile);
        } else {
            $src = imagecreatefromstring(file_get_contents($uploadedFile));
        }

        $dst = imagecreatetruecolor((int)$newwidth, (int)$newheight);

        if ($ext == 'png') {
            //Отключаем режим сопряжения цветов
            imagealphablending($dst, false);
            //Включаем сохранение альфа канала
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, (int)$newwidth, (int)$newheight, (int)$width, (int)$height);

        if ($ext == 'png') {
            imagepng($dst, storage_path('app/public/' . $this->uploadDir . '/' . $this->fileName));
        } else {
            imagejpeg($dst, storage_path('app/public/' . $this->uploadDir . '/' . $this->fileName), 90);
        }
    }

    /**
     * @param $filename
     * @return false|GdImage
     */
    private function imagecreatefromjpegexif($filename): false|GdImage
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
