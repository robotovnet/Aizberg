<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'phone' => 'Phone',
        'code'  => 'Code',
    ],

    'validation_messages' => [
        'phone_required'       => 'Fill in the field :attribute',
        'phone_digits_between' => ':attribute must contain from :min to :max digits in the number',
        'phone_unique'         => ':attribute :input is already used in the system for another staff',
        'code_numeric'         => 'The code must consist only of digits',
        'code_digits'          => 'The code must consist of exactly 6 digits',
    ]
];
