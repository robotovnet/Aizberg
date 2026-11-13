<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'subdomain'             => 'subdomain',
        'city_id'               => 'city',
        'city_name_dative_case' => 'city in the dative case',
        'phone'                 => 'contact phone number',
        'is_active'             => 'activating the shop',
    ],

    'validation_messages' => [
        'subdomain_between'              => ':attribute can contain from :min to :max characters',
        'subdomain_unique'               => 'You have already added a shop with the subdomain :input',
        'city_id_required'               => 'Fill in the field :attribute',
        'city_id_numeric'                => ':attribute must contain a numeric ID',
        'city_id_exists'                 => 'The selected city with ID :input was not found in the system',
        'city_name_dative_case_required' => 'Fill in the field :attribute',
        'city_name_dative_case_between'  => ':attribute can contain from :min to: max characters',
        'phone_required'                 => 'Fill in the field :attribute',
        'phone_digits_between'           => ':attribute can contain from :min to :max digits',
        'is_active_required'             => 'Fill in the field :attribute',
        'is_active_in'                   => 'Valid field values: "Y" or " N"',
    ]
];
