<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'        => 'name of the city',
        'names_lang'   => 'language code',
        'is_default'   => 'default city',
        'timezone'     => 'time zone',
        'country_code' => 'country',
    ],

    'validation_messages' => [
        'names_required'        => 'Fill in the ":attribute" field',
        'timezone_required'     => 'Fill in the ":attribute" field',
        'timezone_timezone'     => 'Incorrect time zone',
        'is_default_required'   => 'Fill in the ":attribute" field',
        'is_default_in'         => 'Valid field values: "Y" or " N"',
        'country_code_required' => 'Fill in the ":attribute" field',
        'country_code_exists'   => 'The country with the code :input does not exist in our system',
        'names_lang_required'   => 'Fill in the ":attribute" field',
        'names_lang_size'       => 'Only two-letter language codes are allowed',
        'names_lang_exists'     => 'The language with the code :input does not exist in our system',
        'names_value_required'  => 'Fill in the ":attribute" field',
        'names_value_between'   => 'The name of the city must contain from :min to :max characters',
    ]
];
