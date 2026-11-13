<?php

declare(strict_types=1);

return [
    // Module Name
    'name' => 'Language',
    // The name of the cookie that stores the client's active language
    'cookie_name' => 'lang',
    // Fallback language code
    'fallback_code' => 'en',
    // Lifetime cookie in minutes
    'lifetime_cookie_in_min' => 525600, // 525600 is number of minutes per year
    // Setting codes
    'settings_codes' => [
        'default_language' => 'DEFAULT_LANGUAGE',
    ]
];
