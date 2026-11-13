<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'country_id'          => 'Country ID',
        'city_id'             => 'City ID',
        'adding_method'       => 'Adding method',
        'warehouses'          => 'Warehouse',
        'transport_companies' => 'Transport company',
    ],

    'validation_messages' => [
        'country_id_required'          => 'Fill in the field :attribute',
        'city_id_required'             => 'Fill in the field :attribute',
        'adding_method_required'       => 'Fill in the field :attribute',
        'warehouses_required'          => 'Fill in the field :attribute',
        'transport_companies_required' => 'Fill in the field :attribute',
        'country_id_numeric'           => 'The :attribute field must contain a number',
        'country_id_min'               => 'The :attribute field must contain a number equal to or greater than one',
        'country_id_exists'            => 'The country with ID = :input does not exist in the system',
        'city_id_numeric'              => 'The :attribute field must contain a number',
        'city_id_min'                  => 'The :attribute field must contain a number equal to or greater than one',
        'city_id_exists'               => 'The city with ID = :input does not exist in the system',
        'adding_method_in'             => 'The :attribute field supports the following values: preset, manual',
        'warehouses_array'             => 'The :attribute field must contain an array of warehouse IDs',
        'transport_companies_array'    => 'The :attribute field must contain an array of TC IDs',
        'warehouses_numeric'           => 'The warehouse ID must be a number',
        'warehouses_min'               => 'The warehouse ID must be a number equal to or greater than one',
        'warehouses_exists'            => 'The warehouse with ID = :input does not exist in the system',
        'transport_companies_numeric'  => 'The transport company ID must be a number',
        'transport_companies_min'      => 'The transport company ID must be a number equal to or greater than one',
        'transport_companies_exists'   => 'Transport company with ID = :input does not exist in the system',
    ]
];
