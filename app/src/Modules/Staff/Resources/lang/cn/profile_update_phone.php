<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'phone' => '?',
        'code'  => '?',
    ],

    'validation_messages' => [
        'phone_required'       => '?',
        'phone_digits_between' => '?',
        'phone_unique'         => '?',
        'code_numeric'         => '?',
        'code_digits'          => '?',
    ]
];
