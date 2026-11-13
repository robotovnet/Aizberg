<?php

declare(strict_types=1);

return [
    'disk' => env('MARKETPLACE_MODULES_DISK', 'public'),
    'icon_upload_dir' => env('MARKETPLACE_MODULES_ICON_DIR', 'marketplace-modules/icons'),
    // Иконка обрезается по центру под эти пропорции и уменьшается до этого размера (px).
    'icon_width' => (int)env('MARKETPLACE_MODULES_ICON_WIDTH', 112),
    'icon_height' => (int)env('MARKETPLACE_MODULES_ICON_HEIGHT', 112),
    'screenshot_disk' => env('MARKETPLACE_MODULES_SCREENSHOT_DISK', 'public'),
    'screenshot_upload_dir' => env('MARKETPLACE_MODULES_SCREENSHOT_DIR', 'marketplace-modules/screenshots'),
    'api_host' => env('API_HOST'),
];
