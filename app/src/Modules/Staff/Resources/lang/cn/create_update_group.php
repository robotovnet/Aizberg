<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'name'                => '?',
        'permissions'         => '?',
        'permissions_policy'  => '?',
    ],

    'validation_messages' => [
        'name_required'               => '?',
        'name_max'                    => '?',
        'name_unique'                 => '?',
        'permissions_required'        => '?',
        'permissions_policy_required' => '?',
        'permissions_policy_max'      => '?',
        'permissions_policy_distinct' => '?',
    ]
];
