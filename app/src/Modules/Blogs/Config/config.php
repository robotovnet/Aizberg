<?php

declare(strict_types=1);

return [
    'disk' => env('BLOGS_DISK', 'public'),
    'preview_upload_dir' => env('BLOGS_PREVIEW_DIR', 'blogs/previews'),
    'api_host' => env('API_HOST'),
    'articles_per_page' => (int) env('BLOGS_ARTICLES_PER_PAGE', 20),
    'comments_per_page' => (int) env('BLOGS_COMMENTS_PER_PAGE', 50),
];
