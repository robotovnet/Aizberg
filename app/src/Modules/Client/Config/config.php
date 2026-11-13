<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | MODULE
    |--------------------------------------------------------------------------
    */

    // Module Name
    'name' => 'Client',

    'clients_per_page' => 10,

    'userpic_disk' => env('CLIENT_USERPIC_DISK', 'public'),
    'userpic_upload_dir' => env('CLIENT_USERPIC_DIR', 'clients/userpics'),
    'api_host' => env('API_HOST'),

    /*
    |--------------------------------------------------------------------------
    | REGISTRATION
    |--------------------------------------------------------------------------
    */

    // List of attached modules when registering
    'registration_modules' => 'city,country,language,currency,timezone,bonusprogram',

    // Where to redirect a new client after registration
    'redirect_path_after_registration' => '/my',

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION
    |--------------------------------------------------------------------------
    */

    // Where to redirect a client after login
    'redirect_path_after_login' => '/my',

    // Where to redirect a client after logout
    'redirect_path_after_logout' => '/',

    // Should remember the client authentication
    'should_remember_auth' => true,

    // Guard driver name
    'guard' => 'client',

    /*
    |--------------------------------------------------------------------------
    | PASSWORD RESTORE
    |--------------------------------------------------------------------------
    */

    // Prefix of routes names
    'routes_names_prefix' => 'clients.password_restore.',

    // Prefix of session variables
    'session_var_prefix' => 'client_password_restore.',

    // Maximum number of code entry attempts
    'max_code_entry_attempts' => 10,

    /*
    |--------------------------------------------------------------------------
    | SETTING CODES
    |--------------------------------------------------------------------------
    */

    'settings_codes' => [
        'show_captcha_at_registration' => 'SHOW_CAPCTHA_AT_REGISTRATION',
        'show_captcha_at_email_recovery' => 'CAPTCHA_AT_EMAIL_RECOVERY',
    ]
];
