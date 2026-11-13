<?php

declare(strict_types=1);

return [
    'disk' => env('SHOWCASE_DISK', 'public'),
    'images_upload_dir' => env('SHOWCASE_IMAGES_DIR', 'showcase/images'),
    'examples_per_page' => (int) env('SHOWCASE_EXAMPLES_PER_PAGE', 20),
    'max_images_per_example' => (int) env('SHOWCASE_MAX_IMAGES', 10),
];
