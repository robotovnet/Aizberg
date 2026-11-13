<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'         => '?',
        'names_lang'    => '?',
        'country_code'  => '?',
        'is_default'    => '?',
        'code'          => '?',
        'symbol'        => '?',
        'extra_charge'  => '?',
    ],

    'validation_messages' => [
        'names_required'        => '?',
        'is_default_required'   => '?',
        'is_default_in'         => '?',
        'names_lang_required'   => '?',
        'names_lang_size'       => '?',
        'names_lang_exists'     => '?',
        'names_value_required'  => '?',
        'names_value_between'   => '?',
        'code_required'         => '?',
        'code_size'             => '?',
        'symbol_required'       => '?',
        'symbol_between'        => '?',
        'extra_charge_required' => '?',
        'extra_charge_numeric'  => '?',
        'extra_charge_between'  => '?',
        'country_code_required' => '?',
        'country_code_exists'   => '?',
        'country_code_unique'   => '?',
    ]
];
