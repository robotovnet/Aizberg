<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Services;

use Modules\Staff\Services\UserpicService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

/**
 * Class IconService
 * @package Modules\TransportCompany\Services
 */
class IconService extends UserpicService
{
    /**
     * IconService constructor.
     */
    public function __construct(UploadedFile $file)
    {
        parent::__construct($file);

        $moduleName = 'transportCompany';
        $this->uploadDir = Config::get($moduleName . '.upload_dir');
    }
}
