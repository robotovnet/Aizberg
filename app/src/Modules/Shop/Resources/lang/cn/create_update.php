<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'subdomain'             => '?',
        'city_id'               => '?',
        'city_name_dative_case' => '?',
        'phone'                 => '?',
        'is_active'             => '?',
    ],

    'validation_messages' => [
        'subdomain_between'              => '?',
        'subdomain_unique'               => '?',
        'city_id_required'               => '?',
        'city_id_numeric'                => '?',
        'city_id_exists'                 => '?',
        'city_name_dative_case_required' => '?',
        'city_name_dative_case_between'  => '?',
        'phone_required'                 => '?',
        'phone_digits_between'           => '?',
        'is_active_required'             => '?',
        'is_active_in'                   => '?',
    ]
];
