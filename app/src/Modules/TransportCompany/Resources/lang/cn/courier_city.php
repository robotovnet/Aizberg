<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'country_id'          => '?',
        'city_id'             => '?',
        'adding_method'       => '?',
        'warehouses'          => '?',
        'transport_companies' => '?',
    ],

    'validation_messages' => [
        'country_id_required'          => '?',
        'city_id_required'             => '?',
        'adding_method_required'       => '?',
        'warehouses_required'          => '?',
        'transport_companies_required' => '?',
        'country_id_numeric'           => '?',
        'country_id_min'               => '?',
        'country_id_exists'            => '?',
        'city_id_numeric'              => '?',
        'city_id_min'                  => '?',
        'city_id_exists'               => '?',
        'adding_method_in'             => '?',
        'warehouses_array'             => '?',
        'transport_companies_array'    => '?',
        'warehouses_numeric'           => '?',
        'warehouses_min'               => '?',
        'warehouses_exists'            => '?',
        'transport_companies_numeric'  => '?',
        'transport_companies_min'      => '?',
        'transport_companies_exists'   => '?',
    ]
];
