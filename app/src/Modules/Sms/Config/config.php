<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | MODULE
    |--------------------------------------------------------------------------
    */

    // Module Name
    'name' => 'Sms',

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'prontosms' => [
            'endpoint'  => 'https://clk.prontosms.ru/sendsms.php',
            'sender'    => env('PRONTOSMS_SENDER'),
            'user'      => env('PRONTOSMS_USER'),
            'password'  => env('PRONTOSMS_PWD'),
            'translite' => 0, // Boolean, accepts 1 or 0
        ],
    ],
];
