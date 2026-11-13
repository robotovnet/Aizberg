<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Password Restore Language Lines
    |--------------------------------------------------------------------------
    */

    'invalid_code'         => 'Invalid recovery code entered.',
    'password_not_changed' => 'Password has not been changed.',
    'email_required'       => 'email field required.',
    'phone_required'       => 'phone field required.',
    'too_many_attempts'    => 'Too many attempts to enter the recovery code.',
    'sms_message_text'     => 'Recovery Code: :code',

    'validation_attributes' => [
        'password' => 'password',
        'code'     => 'code',
        'phone'    => 'phone',
        'email'    => 'email',
        'captcha'  => 'captcha',
    ],

    'validation_messages' => [
        'password_required'     => 'Enter your :attribute',
        'password_between'      => 'The :attribute field must contain from :min to :max characters',
        'password_confirmed'    => 'Password mismatch',
        'code_required'         => 'Enter :attribute',
        'code_numeric'          => 'Only numbers are required in the :attribute field',
        'code_digits'           => 'Exactly 6 digits required',
        'email_required'        => 'Enter your :attribute',
        'email_is_valid'        => 'Check if the :attribute is entered correctly',
        'email_exists'          => 'Account with email :input not registered',
        'phone_required'        => 'Enter your :attribute',
        'phone_digits_between'  => 'The :attribute field must contain from :min to :max characters',
        'phone_exists'          => 'Account with phone number :input not registered',
        'captcha_required'      => 'Enter :attribute',
    ]
];
