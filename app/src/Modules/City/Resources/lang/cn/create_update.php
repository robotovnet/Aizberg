<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'        => '?',
        'names_lang'   => '?',
        'is_default'   => '?',
        'timezone'     => '?',
        'country_code' => '?',
    ],

    'validation_messages' => [
        'names_required'        => '?',
        'timezone_required'     => '?',
        'timezone_timezone'     => '?',
        'is_default_required'   => '?',
        'is_default_in'         => '?',
        'country_code_required' => '?',
        'country_code_exists'   => '?',
        'names_lang_required'   => '?',
        'names_lang_size'       => '?',
        'names_lang_exists'     => '?',
        'names_value_required'  => '?',
        'names_value_between'   => '?',
    ]
];
