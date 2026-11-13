<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'current_password' => 'Current password',
        'password'         => 'Password',
    ],

    'validation_messages' => [
        'current_password_required' => 'Fill in the field :attribute',
        'current_password_between'  => 'The :attribute field must contain from :min to :max characters',
        'password_required'         => 'Fill in the field :attribute',
        'password_between'          => 'The :attribute field must contain from :min to :max characters',
        'password_confirmed'        => "Passwords don't match",
    ]
];
