<?php

declare(strict_types=1);

namespace Modules\Catalog\Services;

use Modules\Staff\Services\UserpicService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

/**
 * Class AdPhotoService
 * @package Modules\Catalog\Services
 */
class AdPhotoService extends UserpicService
{
    /**
     * AdPhotoService constructor.
     */
    public function __construct(UploadedFile $file)
    {
        parent::__construct($file);

        $moduleName = 'catalog';
        $this->uploadDir = Config::get($moduleName . '.upload_ad_photo_dir');
    }
}
