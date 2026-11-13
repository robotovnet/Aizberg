<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'email' => 'Email',
        'code'  => 'Code',
    ],

    'validation_messages' => [
        'email_required' => 'Fill in the field :attribute',
        'email_email'    => 'You entered an incorrect :attribute',
        'email_max'      => 'The maximum allowed length of an e-mail is :max characters',
        'email_unique'   => ':attribute :input is already used in the system for another staff',
        'code_numeric'   => 'The code must consist only of digits',
        'code_digits'    => 'The code must consist of exactly 6 digits',
    ]
];
