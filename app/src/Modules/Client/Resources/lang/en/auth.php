<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    */

    'failed' => 'Invalid login or password',
    'blocked' => 'The user is blocked',

    'validation_attributes' => [
        'phone'    => 'phone',
        'email'    => 'email',
        'password' => 'password',
    ],

    'validation_messages' => [
        'phone_required'      => 'Enter your :attribute',
        'phone_is_valid'      => 'Check if the :attribute is entered correctly',
        'email_required'      => 'Enter your :attribute',
        'email_is_valid'      => 'Check if the :attribute is entered correctly',
        'password_required'   => 'Enter your :attribute',
        'password_min_length' => 'Invalid :attribute. Minimum length is :min characters',
        'password_max_length' => 'Invalid :attribute. Maximum length is :max characters',
    ]
];
