<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MODULE
    |--------------------------------------------------------------------------
    */

    // Module Name
    'name' => 'Staff',

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION
    |--------------------------------------------------------------------------
    */

    // Where to redirect a client after login
    'redirect_path_after_login' => '/dashboard/sales/orders/',

    // Where to redirect a client after logout
    'redirect_path_after_logout' => '/staff/login',

    // Should remember the client authentication
    'should_remember_auth' => false,

    // Guard driver name
    'guard' => 'staff',

    /*
    |--------------------------------------------------------------------------
    | PASSWORD RESTORE
    |--------------------------------------------------------------------------
    */

    // Prefix of routes names
    'routes_names_prefix' => 'staff.password_restore.',

    // Prefix of session variables
    'session_var_prefix' => 'staff_password_restore.',

    // Maximum number of code entry attempts
    'max_code_entry_attempts' => 5,

    /*
    |--------------------------------------------------------------------------
    | USER PICTURE
    |--------------------------------------------------------------------------
    */

    // Folder path where userpic are uploaded
    'upload_dir' => 'userpics',

    // The disk that stores files
    'disk' => 'public',

    // Resize uploaded files to width
    'image_width'  => 152,

    // Resize uploaded files to height
    'image_height' => 152,

    // Crop uploaded files?
    'image_crop'   => false,
];
