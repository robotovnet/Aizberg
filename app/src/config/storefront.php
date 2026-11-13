<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
|
| Settings of the public site (storefront) that consumes this API. They are
| used to build absolute links to products, pages and articles in API
| responses, exports and e-mails.
|
*/

return [
    // Public URL of the storefront. When empty, the host of the current
    // request is used (handy when several city sub-domains share one API).
    'url' => env('STOREFRONT_URL', ''),

    // Optional path prefix inside the storefront, e.g. "shop".
    'prefix_url' => env('STOREFRONT_PREFIX_URL', ''),

    // Product page path relative to the storefront URL.
    // Placeholders: {slug} - slug of the product name, {id} - product ID.
    'product_url_pattern' => env('STOREFRONT_PRODUCT_URL_PATTERN', 'item/{slug}_{id}.html'),

    // City used when the visitor has not chosen one yet.
    'default_city' => env('STOREFRONT_DEFAULT_CITY', 'Москва'),

    // Phone shown when the shop of the visitor's city has no phone number.
    'phone_number' => env('STOREFRONT_PHONE_NUMBER', ''),

    // Client type IDs that are allowed to see "not for everyone" products and sections.
    'not_for_everyone_client_types' => [2],
];
