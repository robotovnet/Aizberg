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
        'is_default'    => '?',
        'price_type_id' => '?',
    ],

    'validation_messages' => [
        'names_required'         => '?',
        'is_default_required'    => '?',
        'is_default_in'          => '?',
        'price_type_id_required' => '?',
        'price_type_id_numeric'  => '?',
        'price_type_id_exists'   => '?',
        'names_lang_required'    => '?',
        'names_lang_size'        => '?',
        'names_lang_exists'      => '?',
        'names_value_required'   => '?',
        'names_value_between'    => '?',
    ]
];
