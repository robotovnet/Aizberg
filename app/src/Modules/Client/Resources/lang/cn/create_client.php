<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Registration Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'phone'        => '?',
        'email'        => '?',
        'password'     => '?',
        'first_name'   => '?',
        'last_name'    => '?',
        'timezone'     => '?',
        'city_id'      => '?',
        'country_code' => '?',
        'captcha'      => '?',
    ],

    'validation_messages' => [
        'first_name_required'   => '?',
        'first_name_between'    => '?',
        'last_name_required'    => '?',
        'last_name_between'     => '?',
        'email_required'        => '?',
        'email_is_valid'        => '?',
        'email_unique'          => '?',
        'email_max'             => '?',
        'phone_required'        => '?',
        'phone_digits_between'  => '?',
        'phone_unique'          => '?',
        'password_required'     => '?',
        'password_between'      => '?',
        'password_confirmed'    => '?',
        'captcha_required'      => '?',
        'captcha_invalid'       => '?',
    ]
];
