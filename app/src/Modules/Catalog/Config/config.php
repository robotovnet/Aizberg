<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | MODULE
    |--------------------------------------------------------------------------
    */

    // Module Name
    'name' => 'Catalog',

    /*
    |--------------------------------------------------------------------------
    | CATALOG SECTIONS ICON
    |--------------------------------------------------------------------------
    */

    // Folder path where userpic are uploaded
    'upload_dir' => 'thumbnail_catalog_sections',

    // The disk that stores files
    'disk' => 'public',

    /*
    |--------------------------------------------------------------------------
    | CATALOG
    |--------------------------------------------------------------------------
    */

    'products_per_page' => 10,

    /*
    |--------------------------------------------------------------------------
    | PRODUCT PHOTOS
    |--------------------------------------------------------------------------
    */

    // Folder path where product photos are uploaded
    'upload_origin_photo_dir' => 'product_photos/origin',
    'upload_micro_photo_dir'  => 'product_photos/micro',
    'upload_mini_photo_dir'   => 'product_photos/mini',

    // Max photo sizes in px (micro - самый маленький размер)
    'photo_width'    => env('CATALOG_MICRO_PHOTO_WIDTH', 240),
    'photo_height'   => env('CATALOG_MICRO_PHOTO_HEIGHT', 240),

    // Max photo sizes in px (mini - средний размер)
    'mini_photo_width'    => env('CATALOG_MINI_PHOTO_WIDTH', 1060),
    'mini_photo_height'   => env('CATALOG_MINI_PHOTO_HEIGHT', 1060),

    /*
    |--------------------------------------------------------------------------
    | PRODUCT PHOTOS FOR ADS
    |--------------------------------------------------------------------------
    */

    'upload_ad_photo_dir' => 'ad_product_photos',

    // Max photo sizes in px
    'ad_photo_width'    => 1000,
    'ad_photo_height'   => 1000,
];
