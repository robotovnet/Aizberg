<?php

return [
    'name' => 'RealProjects',

    // Хост API для абсолютных ссылок на баннеры: https://{API_HOST}/storage/...
    // Если не задан, banner_url отдаётся относительным путём /storage/...
    'api_host' => env('API_HOST'),

    // Диск и каталог для баннеров проектов
    'disk' => 'public',
    'banner_upload_dir' => 'real-projects',
    // Максимальный размер баннера в килобайтах
    'banner_max_size' => 4096,
];
