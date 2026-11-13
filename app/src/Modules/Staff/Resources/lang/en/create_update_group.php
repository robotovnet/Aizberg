<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'name'                => 'Group name',
        'permissions'         => 'Group permissions',
        'permissions_policy'  => 'Permission',
    ],

    'validation_messages' => [
        'name_required'               => 'Fill in the field :attribute',
        'name_max'                    => ':attribute must not be longer than :max characters',
        'name_unique'                 => 'There is already a group with the name :input',
        'permissions_required'        => 'The list of permissions for the group is missing',
        'permissions_policy_required' => 'You must specify at least one :attribute for the group',
        'permissions_policy_max'      => ':attribute must not be longer than :max characters',
        'permissions_policy_distinct' => ':attribute is repeated',
    ]
];
