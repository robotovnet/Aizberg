<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Registration Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'phone'        => 'phone',
        'email'        => 'email',
        'password'     => 'password',
        'first_name'   => 'first name',
        'last_name'    => 'last name',
        'timezone'     => 'timezone',
        'city_id'      => 'city',
        'country_code' => 'country',
        'captcha'      => 'verification code',
    ],

    'validation_messages' => [
        'first_name_required'   => 'Enter your :attribute',
        'first_name_between'    => 'The :attribute field must contain from :min to :max characters',
        'last_name_required'    => 'Enter your :attribute',
        'last_name_between'     => 'The :attribute field must contain from :min to :max characters',
        'email_required'        => 'Enter your :attribute',
        'email_is_valid'        => 'Check if the :attribute is entered correctly',
        'email_unique'          => 'Account with email :input is already registered earlier',
        'email_max'             => 'The :attribute field must not contain more than :max characters',
        'phone_required'        => 'Enter your :attribute',
        'phone_digits_between'  => 'The :attribute field must contain from :min to :max numbers',
        'phone_unique'          => 'Account with phone :input is already registered earlier',
        'password_required'     => 'Enter your :attribute',
        'password_between'      => 'The :attribute field must contain from :min to :max characters',
        'password_confirmed'    => 'Password mismatch',
        'captcha_required'      => 'Enter the code shown in the image',
        'captcha_invalid'       => 'The verification code is incorrect',
    ]
];
