<?php

declare(strict_types=1);

return [
    'disk' => env('IMAGE_UPLOADER_DISK', 'public'),
    'directory' => env('IMAGE_UPLOADER_DIRECTORY', 'images/uploads'),
    'micro_width' => (int) env('CATALOG_MICRO_PHOTO_WIDTH', 240),
    'per_page' => (int) env('IMAGE_UPLOADER_PER_PAGE', 20),
];

